import pandas as pd
import numpy as np
from sklearn.model_selection import train_test_split
from sklearn.preprocessing import LabelEncoder, StandardScaler
from sklearn.ensemble import RandomForestClassifier
from sklearn.pipeline import Pipeline
from sklearn.compose import ColumnTransformer
import joblib
import os

def train_models(csv_path: str, output_dir: str):
    # 1. Load Dataset
    df = pd.read_csv(csv_path)
    
    # Required columns validation and mapping
    expected_cols = ['Jenis Kelamin', 'Umur (bulan)', 'Tinggi Badan (cm)', 'Berat Badan (kg)', 'Stunting', 'Wasting']
    for col in expected_cols:
        if col not in df.columns:
            raise ValueError(f"Kolom wajib '{col}' tidak ditemukan di dataset.")

    # Rename columns to our internal representation
    df = df.rename(columns={
        'Jenis Kelamin': 'jenis_kelamin',
        'Umur (bulan)': 'umur',
        'Tinggi Badan (cm)': 'tinggi',
        'Berat Badan (kg)': 'berat',
        'Stunting': 'stunting',
        'Wasting': 'wasting'
    })

    # 2. Features and Targets
    X = df[['jenis_kelamin', 'umur', 'tinggi', 'berat']]
    y_stunting = df['stunting']
    y_wasting = df['wasting']

    # 3. Define Preprocessing Pipeline
    # Encode 'jenis_kelamin' (Laki-laki -> 1, Perempuan -> 0) based on typical datasets or mapping
    # Note: Let's make it case-insensitive and handle various formats ('L'/'P' or 'M'/'F')
    # According to GEMINI.md, 'M' (Male) or 'F' (Female). 
    X_processed = X.copy()
    X_processed['jenis_kelamin'] = X_processed['jenis_kelamin'].astype(str).str.upper().map({'M': 1, 'F': 0, 'LAKI-LAKI': 1, 'PEREMPUAN': 0, 'L': 1, 'P': 0}).fillna(0)

    # Ensure numeric types
    X_processed['umur'] = pd.to_numeric(X_processed['umur'], errors='coerce').fillna(0)
    X_processed['tinggi'] = pd.to_numeric(X_processed['tinggi'], errors='coerce').fillna(0)
    X_processed['berat'] = pd.to_numeric(X_processed['berat'], errors='coerce').fillna(0)

    # Split dataset for evaluation
    X_train_s, X_test_s, y_train_s, y_test_s = train_test_split(X_processed, y_stunting, test_size=0.2, random_state=42)
    X_train_w, X_test_w, y_train_w, y_test_w = train_test_split(X_processed, y_wasting, test_size=0.2, random_state=42)

    # 4. Train Stunting Classifier
    stunting_model = Pipeline([
        ('scaler', StandardScaler()),
        ('classifier', RandomForestClassifier(n_estimators=100, random_state=42))
    ])
    stunting_model.fit(X_train_s, y_train_s)
    print(f"Stunting model accuracy: {stunting_model.score(X_test_s, y_test_s):.4f}")

    # 5. Train Wasting Classifier
    wasting_model = Pipeline([
        ('scaler', StandardScaler()),
        ('classifier', RandomForestClassifier(n_estimators=100, random_state=42))
    ])
    wasting_model.fit(X_train_w, y_train_w)
    print(f"Wasting model accuracy: {wasting_model.score(X_test_w, y_test_w):.4f}")

    # 6. Save Model Artifacts
    os.makedirs(output_dir, exist_ok=True)
    joblib.dump(stunting_model, os.path.join(output_dir, 'stunting_classifier.joblib'))
    joblib.dump(wasting_model, os.path.join(output_dir, 'wasting_classifier.joblib'))
    print("Models successfully saved to", output_dir)

if __name__ == "__main__":
    current_dir = os.path.dirname(__file__)
    # Note: Using the actual filename present in the directory
    dataset_path = os.path.join(current_dir, 'dataset', 'stunting_wasting_dataset.csv')
    
    if not os.path.exists(dataset_path):
        print(f"Dataset not found at: {dataset_path}")
        sys.exit(1)
        
    models_out_dir = os.path.join(current_dir, 'models')
    train_models(dataset_path, models_out_dir)
