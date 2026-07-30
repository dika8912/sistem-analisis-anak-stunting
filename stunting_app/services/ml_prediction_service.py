import joblib
import os
import numpy as np
import pandas as pd
import warnings

# Suppress sklearn warnings about feature names lacking
warnings.filterwarnings("ignore", category=UserWarning, module="sklearn")

class MLPredictionService:
    def __init__(self, models_dir: str):
        self.models_dir = models_dir
        
        self.stunting_model = None
        self.stunting_encoder = None
        self.stunting_scaler = None
        
        self.wasting_model = None
        self.wasting_encoder = None
        self.wasting_scaler = None
        
        self.gender_encoder = None
        
        # Proactively load models if they exist
        self._load_models_if_needed()

    def _load_models_if_needed(self):
        """Lazy load models if they were created after initialization (e.g. by auto-train)."""
        if not self.stunting_model and os.path.exists(os.path.join(self.models_dir, 'stunting_model.joblib')):
            self.stunting_model = joblib.load(os.path.join(self.models_dir, 'stunting_model.joblib'))
            self.stunting_encoder = joblib.load(os.path.join(self.models_dir, 'stunting_encoder.joblib'))
            self.stunting_scaler = joblib.load(os.path.join(self.models_dir, 'stunting_scaler.joblib'))
            
            self.wasting_model = joblib.load(os.path.join(self.models_dir, 'wasting_model.joblib'))
            self.wasting_encoder = joblib.load(os.path.join(self.models_dir, 'wasting_encoder.joblib'))
            self.wasting_scaler = joblib.load(os.path.join(self.models_dir, 'wasting_scaler.joblib'))
            
            self.gender_encoder = joblib.load(os.path.join(self.models_dir, 'gender_encoder.joblib'))

    def predict(self, gender: str, age_months: int, height_cm: float, weight_kg: float) -> dict:
        """
        Predicts stunting and wasting status with confidence scores.
        """
        self._load_models_if_needed()
        
        if not self.stunting_model or not self.wasting_model:
            return {
                "stunting_status_ml": "model_not_trained",
                "stunting_confidence": 0.0,
                "wasting_status_ml": "model_not_trained",
                "wasting_confidence": 0.0
            }

        # Normalize gender input
        gender_upper = str(gender).upper()
        if gender_upper in ['M', 'L']:
            gender_normalized = 'LAKI-LAKI'
        elif gender_upper in ['F', 'P']:
            gender_normalized = 'PEREMPUAN'
        else:
            gender_normalized = gender_upper
            
        try:
            gender_num = self.gender_encoder.transform([gender_normalized])[0]
        except ValueError:
            gender_num = 0

        input_df = pd.DataFrame([{
            'jenis_kelamin': gender_num,
            'umur': age_months,
            'tinggi': height_cm,
            'berat': weight_kg
        }])

        umur_safe = 1 if age_months == 0 else age_months
        tinggi_safe = 1.0 if height_cm == 0.0 else height_cm
        
        input_df['bb_u'] = weight_kg / umur_safe
        input_df['tb_u'] = height_cm / umur_safe
        input_df['bb_tb'] = weight_kg / tinggi_safe
        input_df['imt'] = weight_kg / ((tinggi_safe / 100) ** 2)

        col_to_scale = input_df.drop('jenis_kelamin', axis=1).columns

        # Stunting Prediction
        input_stunting = input_df.copy()
        input_stunting[col_to_scale] = self.stunting_scaler.transform(input_stunting[col_to_scale])
        stunting_pred_idx = self.stunting_model.predict(input_stunting)[0]
        stunting_probs = self.stunting_model.predict_proba(input_stunting)[0]
        stunting_conf = float(np.max(stunting_probs))
        stunting_status = self.stunting_encoder.inverse_transform([stunting_pred_idx])[0]

        # Wasting Prediction
        input_wasting = input_df.copy()
        input_wasting[col_to_scale] = self.wasting_scaler.transform(input_wasting[col_to_scale])
        wasting_pred_idx = self.wasting_model.predict(input_wasting)[0]
        wasting_probs = self.wasting_model.predict_proba(input_wasting)[0]
        wasting_conf = float(np.max(wasting_probs))
        wasting_status = self.wasting_encoder.inverse_transform([wasting_pred_idx])[0]

        return {
            "stunting_status_ml": str(stunting_status),
            "stunting_confidence": round(stunting_conf, 4),
            "wasting_status_ml": str(wasting_status),
            "wasting_confidence": round(wasting_conf, 4)
        }
