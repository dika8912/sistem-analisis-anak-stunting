import pandas as pd
import numpy as np
import sys
import os
import joblib
from sklearn.model_selection import train_test_split, StratifiedKFold, GridSearchCV
from sklearn.preprocessing import LabelEncoder, MinMaxScaler
from sklearn.tree import DecisionTreeClassifier
from imblearn.over_sampling import SMOTENC
import warnings
warnings.filterwarnings("ignore")

STUNTING_LABEL_MAP = {
    'Normal': 'zona_aman',
    'Stunted': 'zona_sedang',
    'Severely Stunted': 'zona_bahaya',
    'Tall': 'zona_aman',
}

WASTING_LABEL_MAP = {
    'Normal weight': 'zona_aman',
    'Normal Weight': 'zona_aman',
    'Risk of Overweight': 'zona_sedang',
    'Overweight': 'zona_bahaya',
    'Obese': 'zona_bahaya',
    'Underweight': 'zona_sedang',
    'Severely Underweight': 'zona_bahaya',
    'Wasted': 'zona_sedang',
    'Severely Wasted': 'zona_bahaya',
}

def add_engineered_features(df):
    df = df.copy()
    umur_safe = df['umur'].replace(0, 1)
    tinggi_safe = df['tinggi'].replace(0, 1)
    
    df['bb_u'] = df['berat'] / umur_safe
    df['tb_u'] = df['tinggi'] / umur_safe
    df['bb_tb'] = df['berat'] / tinggi_safe
    df['imt'] = df['berat'] / ((tinggi_safe / 100) ** 2)
    return df

def train_and_save_model(X, y, target_name, output_dir, scaler, gender_encoder):
    target_encoder = LabelEncoder()
    y_encoded = target_encoder.fit_transform(y)
    
    X_train, X_test, y_train, y_test = train_test_split(X, y_encoded, test_size=0.2, random_state=42, stratify=y_encoded)
    
    col_to_scale = X_train.drop('jenis_kelamin', axis=1).columns
    X_train[col_to_scale] = scaler.fit_transform(X_train[col_to_scale])
    X_test[col_to_scale] = scaler.transform(X_test[col_to_scale])
    
    sm = SMOTENC(categorical_features=[0], random_state=42)
    X_train_res, y_train_res = sm.fit_resample(X_train, y_train)
    
    dt = DecisionTreeClassifier(random_state=42)
    param_grid = {
        'criterion': ['gini', 'entropy'],
        'max_depth': [3, 5, 7, 9, None],
        'min_samples_split': [2, 5, 10],
        'min_samples_leaf': [1, 2, 5]
    }
    skf = StratifiedKFold(n_splits=5, shuffle=True, random_state=42)
    grid_search = GridSearchCV(
        estimator=dt,
        param_grid=param_grid,
        cv=skf,
        scoring='accuracy',
        n_jobs=-1
    )
    grid_search.fit(X_train_res, y_train_res)
    
    best_model = grid_search.best_estimator_
    acc = best_model.score(X_test, y_test)
    print(f"[{target_name}] Model Accuracy: {acc * 100:.2f}%")
    
    joblib.dump(best_model, os.path.join(output_dir, f'{target_name}_model.joblib'))
    joblib.dump(target_encoder, os.path.join(output_dir, f'{target_name}_encoder.joblib'))
    joblib.dump(scaler, os.path.join(output_dir, f'{target_name}_scaler.joblib'))

def train_models(csv_path: str, output_dir: str):
    print("Loading dataset...")
    df = pd.read_csv(csv_path)
    df = df.rename(columns={
        'Jenis Kelamin': 'jenis_kelamin',
        'Umur (bulan)': 'umur',
        'Tinggi Badan (cm)': 'tinggi',
        'Berat Badan (kg)': 'berat',
        'Stunting': 'stunting',
        'Wasting': 'wasting'
    })
    df = df.drop_duplicates()
    
    X = df[['jenis_kelamin', 'umur', 'tinggi', 'berat']].copy()
    X['umur'] = pd.to_numeric(X['umur'], errors='coerce').fillna(0)
    X['tinggi'] = pd.to_numeric(X['tinggi'], errors='coerce').fillna(0)
    X['berat'] = pd.to_numeric(X['berat'], errors='coerce').fillna(0)
    X = add_engineered_features(X)
    
    gender_encoder = LabelEncoder()
    X['jenis_kelamin'] = X['jenis_kelamin'].astype(str).str.upper()
    X['jenis_kelamin'] = gender_encoder.fit_transform(X['jenis_kelamin'])
    
    y_stunting = df['stunting'].map(STUNTING_LABEL_MAP)
    y_wasting = df['wasting'].map(WASTING_LABEL_MAP)
    
    valid_mask = y_stunting.notna() & y_wasting.notna()
    X = X[valid_mask]
    y_stunting = y_stunting[valid_mask]
    y_wasting = y_wasting[valid_mask]
    
    os.makedirs(output_dir, exist_ok=True)
    joblib.dump(gender_encoder, os.path.join(output_dir, 'gender_encoder.joblib'))
    
    print("Training Stunting Model...")
    train_and_save_model(X.copy(), y_stunting, "stunting", output_dir, MinMaxScaler(), gender_encoder)
    
    print("Training Wasting Model...")
    train_and_save_model(X.copy(), y_wasting, "wasting", output_dir, MinMaxScaler(), gender_encoder)
    
    print(f"All models and artifacts saved to {output_dir}")

if __name__ == "__main__":
    current_dir = os.path.dirname(__file__)
    dataset_path = os.path.join(current_dir, 'dataset', 'stunting_wasting_dataset.csv')
    if not os.path.exists(dataset_path):
        print(f"Dataset not found at: {dataset_path}")
        sys.exit(1)
        
    models_out_dir = os.path.join(current_dir, 'models')
    train_models(dataset_path, models_out_dir)
