import joblib
import os
import numpy as np
import warnings

# Suppress sklearn warnings about feature names lacking
warnings.filterwarnings("ignore", category=UserWarning, module="sklearn")

class MLPredictionService:
    def __init__(self, models_dir: str):
        self.stunting_model_path = os.path.join(models_dir, 'stunting_classifier.joblib')
        self.wasting_model_path = os.path.join(models_dir, 'wasting_classifier.joblib')
        
        self.stunting_model = None
        self.wasting_model = None
        
        # Proactively load models if they exist
        if os.path.exists(self.stunting_model_path):
            self.stunting_model = joblib.load(self.stunting_model_path)
        if os.path.exists(self.wasting_model_path):
            self.wasting_model = joblib.load(self.wasting_model_path)

    def predict(self, gender: str, age_months: int, height_cm: float, weight_kg: float) -> dict:
        """
        Predicts stunting and wasting status with confidence scores.
        """
        if not self.stunting_model or not self.wasting_model:
            return {
                "stunting_status_ml": "model_not_trained",
                "stunting_confidence": 0.0,
                "wasting_status_ml": "model_not_trained",
                "wasting_confidence": 0.0
            }

        # Pre-process inputs matching training format
        # Based on our mapping in train.py: 'M'/'L' -> 1.0, 'F'/'P' -> 0.0
        gender_upper = str(gender).upper()
        gender_num = 1.0 if gender_upper in ['M', 'L', 'LAKI-LAKI'] else 0.0
        
        # Model expects: ['jenis_kelamin', 'umur', 'tinggi', 'berat']
        # Note: Scikit-learn Pipeline expects 2D array or DataFrame. A DataFrame is safer with ColumnTransformer.
        import pandas as pd
        input_df = pd.DataFrame([{
            'jenis_kelamin': gender_num,
            'umur': age_months,
            'tinggi': height_cm,
            'berat': weight_kg
        }])

        # Stunting Prediction
        stunting_pred = self.stunting_model.predict(input_df)[0]
        stunting_probs = self.stunting_model.predict_proba(input_df)[0]
        stunting_conf = float(np.max(stunting_probs))

        # Wasting Prediction
        wasting_pred = self.wasting_model.predict(input_df)[0]
        wasting_probs = self.wasting_model.predict_proba(input_df)[0]
        wasting_conf = float(np.max(wasting_probs))

        return {
            "stunting_status_ml": str(stunting_pred),
            "stunting_confidence": round(stunting_conf, 4),
            "wasting_status_ml": str(wasting_pred),
            "wasting_confidence": round(wasting_conf, 4)
        }
