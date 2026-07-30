import sys, os
sys.path.insert(0, os.path.join(os.path.dirname(__file__), '..', '..'))

import joblib
from stunting_app.config.settings import settings
from stunting_app.services.ml_prediction_service import MLPredictionService

m = joblib.load(settings.MODELS_DIR + '/stunting_model.joblib')
w = joblib.load(settings.MODELS_DIR + '/wasting_model.joblib')
print('Stunting classes (baru):', m.classes_)
print('Wasting classes (baru):', w.classes_)
print()

svc = MLPredictionService(models_dir=settings.MODELS_DIR)
tests = [
    ('M', 6,  65.0, 7.0,   'bayi normal'),
    ('F', 24, 82.0, 9.5,   'balita stunted'),
    ('M', 12, 68.0, 6.5,   'bayi severely stunted'),
    ('F', 36, 90.0, 11.0,  'balita normal'),
]
for gender, age, h, w_kg, desc in tests:
    r = svc.predict(gender, age, h, w_kg)
    stunting = r['stunting_status_ml']
    sc = r['stunting_confidence'] * 100
    wasting = r['wasting_status_ml']
    wc = r['wasting_confidence'] * 100
    print(f'{desc}: stunting={stunting} ({sc:.0f}%), wasting={wasting} ({wc:.0f}%)')
