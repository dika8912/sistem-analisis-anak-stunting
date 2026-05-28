"""
Seed script for food_recommendations table.
Covers semua status WHO + ML: normal, stunted, severely_stunted, tall,
wasted, severely_wasted, overweight, obese, risk_of_overweight, underweight, severely_underweight
dengan pembagian kelompok usia: 0-5, 6-11, 12-23, 24-60 bulan.

Usage:
    python -m stunting_app.seeds.food_recommendation_seed
"""

import sys
import os
import asyncio

sys.path.insert(0, os.path.abspath(os.path.join(os.path.dirname(__file__), "../../")))

from sqlalchemy import select
from sqlalchemy.ext.asyncio import create_async_engine, async_sessionmaker

from stunting_app.config.settings import settings
from stunting_app.models.food_recommendation import FoodRecommendation

DATABASE_URL = (
    f"mysql+aiomysql://{settings.DB_USER}:{settings.DB_PASSWORD}"
    f"@{settings.DB_HOST}:{settings.DB_PORT}/{settings.DB_NAME}"
)

engine = create_async_engine(DATABASE_URL, echo=False)
async_session_factory = async_sessionmaker(bind=engine, expire_on_commit=False)

# ─────────────────────────────────────────────────────────────────────────────
# Data rekomendasi makanan per status & kelompok usia
# ─────────────────────────────────────────────────────────────────────────────
RECOMMENDATIONS = [

    # ──────────── NORMAL (0-5 bulan) ─────────────────────────────────────────
    {"stunting_status": "normal", "min_age_months": 0, "max_age_months": 5,
     "recommendation_text": "Berikan ASI eksklusif setiap 2-3 jam sekali. ASI cukup memenuhi seluruh kebutuhan gizi bayi pada usia ini."},

    # ──────────── NORMAL (6-11 bulan) ────────────────────────────────────────
    {"stunting_status": "normal", "min_age_months": 6, "max_age_months": 11,
     "recommendation_text": "Mulai MPASI dengan tekstur halus: bubur susu, puree sayuran, puree buah. Tetap lanjutkan ASI sebagai makanan utama."},
    {"stunting_status": "normal", "min_age_months": 6, "max_age_months": 11,
     "recommendation_text": "Kenalkan sumber protein: telur rebus halus, hati ayam, tahu, dan tempe yang dihaluskan."},

    # ──────────── NORMAL (12-23 bulan) ───────────────────────────────────────
    {"stunting_status": "normal", "min_age_months": 12, "max_age_months": 23,
     "recommendation_text": "Berikan makanan keluarga dengan tekstur yang lebih padat: nasi tim, lauk pauk, sayur, dan buah. Frekuensi makan 3 kali sehari + 2 selingan."},
    {"stunting_status": "normal", "min_age_months": 12, "max_age_months": 23,
     "recommendation_text": "Sumber protein hewani penting: telur, ikan, ayam, daging sapi. Berikan minimal 1 porsi setiap hari."},

    # ──────────── NORMAL (24-60 bulan) ───────────────────────────────────────
    {"stunting_status": "normal", "min_age_months": 24, "max_age_months": 60,
     "recommendation_text": "Pastikan pola makan seimbang: karbohidrat, protein hewani dan nabati, sayuran berwarna, serta buah-buahan segar setiap hari."},
    {"stunting_status": "normal", "min_age_months": 24, "max_age_months": 60,
     "recommendation_text": "Lanjutkan pemantauan tumbuh kembang bulanan di Posyandu. Lengkapi imunisasi sesuai jadwal."},

    # ──────────── STUNTED (0-5 bulan) ────────────────────────────────────────
    {"stunting_status": "stunted", "min_age_months": 0, "max_age_months": 5,
     "recommendation_text": "Tingkatkan frekuensi dan durasi menyusui. Pastikan pelekatan (latch-on) bayi benar agar ASI yang dihisap maksimal."},
    {"stunting_status": "stunted", "min_age_months": 0, "max_age_months": 5,
     "recommendation_text": "Segera konsultasikan ke dokter atau tenaga kesehatan untuk evaluasi pertumbuhan dan intervensi gizi dini."},

    # ──────────── STUNTED (6-11 bulan) ───────────────────────────────────────
    {"stunting_status": "stunted", "min_age_months": 6, "max_age_months": 11,
     "recommendation_text": "Perkaya MPASI dengan sumber protein hewani: telur, hati ayam, dan ikan. Tambahkan minyak atau santan untuk meningkatkan densitas kalori."},
    {"stunting_status": "stunted", "min_age_months": 6, "max_age_months": 11,
     "recommendation_text": "Berikan MPASI lebih sering (4-5 kali sehari) dengan porsi yang ditingkatkan secara bertahap. Lanjutkan ASI."},

    # ──────────── STUNTED (12-23 bulan) ──────────────────────────────────────
    {"stunting_status": "stunted", "min_age_months": 12, "max_age_months": 23,
     "recommendation_text": "Berikan makanan padat gizi: nasi dengan lauk protein hewani (ikan, telur, ayam) ditambah sayuran hijau dan kacang-kacangan setiap hari."},
    {"stunting_status": "stunted", "min_age_months": 12, "max_age_months": 23,
     "recommendation_text": "Konsultasi ke Puskesmas untuk mendapatkan Makanan Tambahan (PMT) dan pemantauan pertumbuhan intensif."},

    # ──────────── STUNTED (24-60 bulan) ──────────────────────────────────────
    {"stunting_status": "stunted", "min_age_months": 24, "max_age_months": 60,
     "recommendation_text": "Tingkatkan asupan protein hewani: ikan, telur, daging, susu. Berikan minimal 2 porsi protein per hari untuk mendukung kejar tumbuh."},
    {"stunting_status": "stunted", "min_age_months": 24, "max_age_months": 60,
     "recommendation_text": "Berikan suplemen zinc dan vitamin A sesuai rekomendasi dokter untuk mendukung perbaikan status gizi."},

    # ──────────── SEVERELY STUNTED (0-5 bulan) ───────────────────────────────
    {"stunting_status": "severely_stunted", "min_age_months": 0, "max_age_months": 5,
     "recommendation_text": "SEGERA bawa ke fasilitas kesehatan. Bayi dengan stunting berat pada usia ini membutuhkan evaluasi medis menyeluruh dan intervensi segera."},

    # ──────────── SEVERELY STUNTED (6-11 bulan) ──────────────────────────────
    {"stunting_status": "severely_stunted", "min_age_months": 6, "max_age_months": 11,
     "recommendation_text": "Rujuk ke dokter spesialis anak atau Puskesmas dengan rawat inap jika perlu. Ikuti program tatalaksana gizi buruk yang ditetapkan tenaga kesehatan."},
    {"stunting_status": "severely_stunted", "min_age_months": 6, "max_age_months": 11,
     "recommendation_text": "Berikan F-100 atau formula khusus untuk pemulihan gizi (therapeutic food) sesuai arahan dokter. Jangan sembarangan memberi suplemen tanpa resep."},

    # ──────────── SEVERELY STUNTED (12-23 bulan) ─────────────────────────────
    {"stunting_status": "severely_stunted", "min_age_months": 12, "max_age_months": 23,
     "recommendation_text": "Ikuti program Pemberian Makanan Tambahan Pemulihan (PMT-P) dari Puskesmas. Pantau berat dan tinggi badan setiap 2 minggu."},
    {"stunting_status": "severely_stunted", "min_age_months": 12, "max_age_months": 23,
     "recommendation_text": "Berikan makanan padat kalori dan protein tinggi: telur, ikan, kacang hijau, dan produk susu. Tambahkan minyak kelapa atau santan untuk meningkatkan kalori."},

    # ──────────── SEVERELY STUNTED (24-60 bulan) ─────────────────────────────
    {"stunting_status": "severely_stunted", "min_age_months": 24, "max_age_months": 60,
     "recommendation_text": "Konsultasi intensif dengan dokter anak dan ahli gizi klinis. Ikuti terapi gizi jangka panjang dan pemantauan pertumbuhan ketat."},
    {"stunting_status": "severely_stunted", "min_age_months": 24, "max_age_months": 60,
     "recommendation_text": "Berikan makanan bergizi tinggi 5-6 kali sehari: protein hewani, kacang-kacangan, sayuran hijau, dan karbohidrat kompleks. Hindari makanan rendah gizi."},

    # ──────────── TALL (0-5 bulan) ────────────────────────────────────────────
    {"stunting_status": "tall", "min_age_months": 0, "max_age_months": 5,
     "recommendation_text": "Pertumbuhan tinggi bayi di atas rata-rata. Lanjutkan ASI eksklusif dan pantau perkembangan secara rutin."},

    # ──────────── TALL (6-60 bulan) ───────────────────────────────────────────
    {"stunting_status": "tall", "min_age_months": 6, "max_age_months": 60,
     "recommendation_text": "Tinggi badan anak berada di atas rata-rata (tall). Pertahankan pola makan seimbang dan pastikan berat badan juga proporsional terhadap tinggi."},
    {"stunting_status": "tall", "min_age_months": 6, "max_age_months": 60,
     "recommendation_text": "Lakukan pemantauan rutin di Posyandu untuk memastikan tinggi badan yang baik ini disertai berat badan yang ideal."},

    # ──────────── WASTED (0-5 bulan) ─────────────────────────────────────────
    {"stunting_status": "wasted", "min_age_months": 0, "max_age_months": 5,
     "recommendation_text": "Tingkatkan frekuensi menyusui. Bayi kurus perlu lebih banyak ASI. Konsultasikan ke bidan atau dokter jika berat badan tidak naik dalam 2 minggu."},

    # ──────────── WASTED (6-11 bulan) ────────────────────────────────────────
    {"stunting_status": "wasted", "min_age_months": 6, "max_age_months": 11,
     "recommendation_text": "Perkaya MPASI dengan tambahan lemak sehat: minyak ikan, minyak zaitun, atau santan. Tambahkan telur dan hati ayam untuk protein dan zat besi."},
    {"stunting_status": "wasted", "min_age_months": 6, "max_age_months": 11,
     "recommendation_text": "Bawa anak ke Puskesmas untuk mendapatkan PMT dan pemantauan berat badan mingguan."},

    # ──────────── WASTED (12-60 bulan) ───────────────────────────────────────
    {"stunting_status": "wasted", "min_age_months": 12, "max_age_months": 60,
     "recommendation_text": "Berikan makanan padat kalori setiap 3 jam: nasi dengan lauk protein, selingan kacang-kacangan, pisang, atau susu. Hindari makanan yang mengenyangkan tapi rendah gizi."},
    {"stunting_status": "wasted", "min_age_months": 12, "max_age_months": 60,
     "recommendation_text": "Periksa kemungkinan penyakit penyerta (infeksi, diare, TB anak) yang bisa menjadi penyebab kekurusan. Segera ke dokter untuk pemeriksaan lanjutan."},

    # ──────────── SEVERELY WASTED (0-5 bulan) ────────────────────────────────
    {"stunting_status": "severely_wasted", "min_age_months": 0, "max_age_months": 5,
     "recommendation_text": "DARURAT GIZI. Segera bawa ke IGD atau Puskesmas terdekat. Bayi kurus berat sangat berisiko dehidrasi dan komplikasi serius."},

    # ──────────── SEVERELY WASTED (6-60 bulan) ───────────────────────────────
    {"stunting_status": "severely_wasted", "min_age_months": 6, "max_age_months": 60,
     "recommendation_text": "GIZI BURUK BERAT. Ikuti protokol tatalaksana gizi buruk: fase stabilisasi dengan F-75 lalu F-100 sesuai arahan dokter. Tidak boleh ditangani sendiri di rumah."},
    {"stunting_status": "severely_wasted", "min_age_months": 6, "max_age_months": 60,
     "recommendation_text": "Pantau tanda bahaya: lemas, tidak mau makan/minum, sesak napas, atau bengkak di kaki. Jika muncul salah satu, segera ke rumah sakit."},

    # ──────────── RISK OF OVERWEIGHT (0-5 bulan) ─────────────────────────────
    {"stunting_status": "risk_of_overweight", "min_age_months": 0, "max_age_months": 5,
     "recommendation_text": "Berat badan bayi mendekati batas atas. Pastikan bayi mendapat ASI on-demand, bukan dijadwalkan terlalu ketat. Jangan tambahkan MPASI sebelum 6 bulan."},

    # ──────────── RISK OF OVERWEIGHT (6-11 bulan) ────────────────────────────
    {"stunting_status": "risk_of_overweight", "min_age_months": 6, "max_age_months": 11,
     "recommendation_text": "Perhatikan kualitas MPASI: utamakan sayuran, buah, protein tanpa lemak. Kurangi makanan manis dan makanan instan. Berikan air putih sebagai minuman utama."},

    # ──────────── RISK OF OVERWEIGHT (12-23 bulan) ───────────────────────────
    {"stunting_status": "risk_of_overweight", "min_age_months": 12, "max_age_months": 23,
     "recommendation_text": "Batasi camilan tinggi gula dan lemak jenuh. Perbanyak sayuran dan buah segar. Pastikan anak aktif bermain dan bergerak setiap hari."},
    {"stunting_status": "risk_of_overweight", "min_age_months": 12, "max_age_months": 23,
     "recommendation_text": "Pantau pola makan: hindari makan di depan layar TV/gadget. Buat jadwal makan yang teratur dan konsisten."},

    # ──────────── RISK OF OVERWEIGHT (24-60 bulan) ───────────────────────────
    {"stunting_status": "risk_of_overweight", "min_age_months": 24, "max_age_months": 60,
     "recommendation_text": "Arahkan ke pola makan isi piringku: setengah piring sayur dan buah, seperempat protein, seperempat karbohidrat. Kurangi minuman manis dan gorengan."},
    {"stunting_status": "risk_of_overweight", "min_age_months": 24, "max_age_months": 60,
     "recommendation_text": "Tingkatkan aktivitas fisik: ajak anak bermain di luar rumah minimal 60 menit sehari. Konsultasi ke dokter jika berat badan terus naik tidak proporsional."},

    # ──────────── OVERWEIGHT (0-5 bulan) ─────────────────────────────────────
    {"stunting_status": "overweight", "min_age_months": 0, "max_age_months": 5,
     "recommendation_text": "Berat badan bayi melebihi ideal. Lanjutkan ASI eksklusif, hindari penggunaan susu formula berlebih. Konsultasikan ke dokter anak untuk evaluasi."},

    # ──────────── OVERWEIGHT (6-11 bulan) ────────────────────────────────────
    {"stunting_status": "overweight", "min_age_months": 6, "max_age_months": 11,
     "recommendation_text": "Kurangi MPASI yang tinggi karbohidrat sederhana (tepung, bubur instan berasa manis). Perbanyak pure sayuran dan buah tanpa tambahan gula."},

    # ──────────── OVERWEIGHT (12-60 bulan) ───────────────────────────────────
    {"stunting_status": "overweight", "min_age_months": 12, "max_age_months": 60,
     "recommendation_text": "Jangan diet ketat pada anak. Fokus pada perbaikan kualitas makanan: kurangi makanan olahan, gorengan, minuman manis. Perbanyak sayur dan buah."},
    {"stunting_status": "overweight", "min_age_months": 12, "max_age_months": 60,
     "recommendation_text": "Dorong aktivitas fisik aktif setiap hari. Batasi screen time maksimal 1 jam per hari. Kontrol ke dokter anak setiap bulan untuk memantau IMT."},

    # ──────────── OBESE (0-11 bulan) ─────────────────────────────────────────
    {"stunting_status": "obese", "min_age_months": 0, "max_age_months": 11,
     "recommendation_text": "Berat badan jauh di atas normal. Segera konsultasi ke dokter anak untuk evaluasi hormonal dan pola makan. Jangan mencoba mengurangi asupan secara drastis tanpa pengawasan medis."},

    # ──────────── OBESE (12-60 bulan) ────────────────────────────────────────
    {"stunting_status": "obese", "min_age_months": 12, "max_age_months": 60,
     "recommendation_text": "Konsultasi dengan dokter anak dan ahli gizi untuk program pengelolaan berat badan yang aman. Hindari makanan ultra-proses, fast food, dan minuman berpemanis."},
    {"stunting_status": "obese", "min_age_months": 12, "max_age_months": 60,
     "recommendation_text": "Prioritaskan aktivitas fisik yang menyenangkan: berenang, berlari, bersepeda. Target bukan menurunkan berat badan, tapi menahan laju kenaikan sambil tinggi badan terus tumbuh."},

    # ──────────── UNDERWEIGHT / SEVERELY UNDERWEIGHT (fallback dari WHO WAZ) ─
    {"stunting_status": "underweight", "min_age_months": 0, "max_age_months": 60,
     "recommendation_text": "Berat badan di bawah normal untuk usia ini. Tingkatkan asupan kalori dan protein: telur, ikan, kacang-kacangan, dan susu. Pantau di Posyandu setiap bulan."},

    {"stunting_status": "severely_underweight", "min_age_months": 0, "max_age_months": 60,
     "recommendation_text": "Berat badan sangat kurang. Segera bawa ke Puskesmas atau dokter anak untuk tatalaksana gizi kurang berat dan pemeriksaan penyakit penyerta."},
]


async def seed_food_recommendations():
    async with async_session_factory() as session:
        # Cek apakah sudah ada data
        result = await session.execute(select(FoodRecommendation).limit(1))
        if result.scalar_one_or_none():
            print("food_recommendations sudah terisi. Skip seeding.")
            print("Gunakan --force untuk menghapus dan mengisi ulang.")
            return

        records = [FoodRecommendation(**data) for data in RECOMMENDATIONS]
        session.add_all(records)
        await session.commit()
        print(f"Berhasil menyimpan {len(records)} rekomendasi makanan.")

        # Ringkasan per status
        status_counts = {}
        for r in RECOMMENDATIONS:
            s = r["stunting_status"]
            status_counts[s] = status_counts.get(s, 0) + 1
        print("\nRingkasan per status:")
        for status, count in sorted(status_counts.items()):
            print(f"  {status}: {count} rekomendasi")


async def seed_food_recommendations_force():
    """Hapus semua data lama lalu isi ulang."""
    async with async_session_factory() as session:
        existing = await session.execute(select(FoodRecommendation))
        rows = existing.scalars().all()
        for row in rows:
            await session.delete(row)
        await session.commit()
        print(f"Dihapus {len(rows)} data lama.")

    await seed_food_recommendations()


if __name__ == "__main__":
    import sys
    force = "--force" in sys.argv
    if force:
        asyncio.run(seed_food_recommendations_force())
    else:
        asyncio.run(seed_food_recommendations())
