import urllib.request
import urllib.error
import json

BASE_URL = "http://localhost:5601/api"

def print_step(title):
    print(f"\n{'='*50}\n🚀 {title}\n{'='*50}")

def fetch(url, data=None, token=None, method="POST"):
    headers = {'Content-Type': 'application/json'}
    if token:
        headers['Authorization'] = f"Bearer {token}"
    
    req_data = json.dumps(data).encode() if data else None
    req = urllib.request.Request(f"{BASE_URL}{url}", data=req_data, headers=headers, method=method)
    
    try:
        res = urllib.request.urlopen(req)
        return json.loads(res.read().decode())
    except urllib.error.HTTPError as e:
        print(f"❌ Error HTTP {e.code}: {e.read().decode()}")
        return None

# 1. Health Check
print_step("1. Health Check (GET /)")
try:
    res = urllib.request.urlopen("http://localhost:5601/")
    print("✅", json.loads(res.read().decode()))
except Exception as e:
    print("❌ Server belum menyala:", e)
    exit(1)

# 2. Test /api/calculate
print_step("2. Test Deteksi On-the-fly (POST /api/calculate)")
calc_payload = {"gender": "F", "age_in_months": 24, "height_cm": 80.5, "weight_kg": 10.2}
print("Mengirim payload:", calc_payload)
calc_res = fetch("/calculate", calc_payload)
if calc_res:
    print("✅ WHO Status:", calc_res['who_calculation']['stunting_status_who'])
    print("✅ Z-Score HAZ:", calc_res['who_calculation']['haz_zscore'])

# 3. Test /api/predict
print_step("3. Test ML Predict (POST /api/predict)")
pred_res = fetch("/predict", calc_payload)
if pred_res:
    print("✅ ML Status:", pred_res['ml_prediction']['stunting_status_ml'])
    print("✅ ML Confidence:", pred_res['ml_prediction']['stunting_confidence'])

print("\n🎉 Semua sistem berjalan lancar secara lokal!")
