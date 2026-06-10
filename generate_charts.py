import pandas as pd
import numpy as np
import matplotlib.pyplot as plt
import os

def smooth(y, box_pts):
    box = np.ones(box_pts)/box_pts
    y_smooth = np.convolve(y, box, mode='same')
    # Handle edges manually
    y_smooth[0] = y.iloc[0] if isinstance(y, pd.Series) else y[0]
    y_smooth[-1] = y.iloc[-1] if isinstance(y, pd.Series) else y[-1]
    return y_smooth

def plot_bmi_chart(df, gender_label, title, output_path):
    # Filter dataset
    df_g = df[df['Jenis Kelamin'].str.lower().str.startswith(gender_label.lower())].copy()
    
    # Calculate BMI
    df_g['Height_m'] = df_g['Tinggi Badan (cm)'] / 100.0
    df_g['BMI'] = df_g['Berat Badan (kg)'] / (df_g['Height_m'] ** 2)
    
    # Clean anomalies
    df_g = df_g[(df_g['BMI'] > 10) & (df_g['BMI'] < 30)]
    
    # Group by age to get stats
    ages = np.arange(0, 61)
    means = []
    stds = []
    
    for a in ages:
        # Get data around this age for smoother curves
        subset = df_g[(df_g['Umur (bulan)'] >= a - 2) & (df_g['Umur (bulan)'] <= a + 2)]
        if len(subset) < 5:
            subset = df_g[(df_g['Umur (bulan)'] >= a - 5) & (df_g['Umur (bulan)'] <= a + 5)]
            
        if not subset.empty:
            means.append(subset['BMI'].mean())
            stds.append(subset['BMI'].std())
        else:
            means.append(np.nan)
            stds.append(np.nan)
            
    means = pd.Series(means).interpolate().bfill().ffill().values
    stds = pd.Series(stds).interpolate().bfill().ffill().values
    
    # Smooth the curves
    means = smooth(means, 5)
    stds = smooth(stds, 5)
    
    # Calculate SD lines
    sd3 = means + 3 * stds
    sd2 = means + 2 * stds
    sd1 = means + 1 * stds
    sd_minus2 = means - 2 * stds
    sd_minus3 = means - 3 * stds

    # Plotting
    plt.figure(figsize=(10, 6))
    
    # Convert age from months to years/months for x-axis if desired, but months is easier.
    # The uploaded image has x-axis in years/months (e.g., 2,0  2,3  2,6 ... 5,0)
    # We will just plot months 0 to 60.
    
    plt.plot(ages, sd2, color='black', linewidth=1)
    plt.plot(ages, sd1, color='black', linewidth=1)
    plt.plot(ages, sd_minus2, color='black', linewidth=1)
    plt.plot(ages, sd_minus3, color='black', linewidth=1)
    
    # Fills
    # Red: > +2 SD
    plt.fill_between(ages, sd2, sd3 + 2, color='#ef5350', alpha=0.8, label='Overweight')
    
    # Orange: +1 SD to +2 SD
    plt.fill_between(ages, sd1, sd2, color='#ffb74d', alpha=0.8, label='At risk of overweight')
    
    # White/Light Green for Normal: -2 SD to +1 SD
    # The image showed white with grid for normal, but let's use a very light green or just white.
    # Let's use white to match the image, or a very light green.
    plt.fill_between(ages, sd_minus2, sd1, color='#ffffff', alpha=1.0, label='Normal')
    
    # Yellow/Greenish for Underweight: < -2 SD
    plt.fill_between(ages, sd_minus3 - 2, sd_minus2, color='#cddc39', alpha=0.8, label='Underweight')
    
    # Formatting
    plt.xlim(0, 60)
    plt.ylim(10, 35)
    
    plt.title(title, fontsize=16, fontweight='bold', pad=20)
    plt.xlabel('Age (months)', fontsize=12)
    plt.ylabel('BMI (kg/m²)', fontsize=12)
    
    # Grid
    plt.grid(True, which='both', linestyle='-', linewidth=0.5, color='lightgrey', alpha=0.7)
    plt.minorticks_on()
    plt.grid(True, which='minor', linestyle=':', linewidth=0.5, color='lightgrey')

    # Add text labels on the right margin
    plt.text(60.5, sd2[-1] + (sd3[-1]-sd2[-1])/2, 'overweight', verticalalignment='center', fontsize=9)
    plt.text(60.5, sd1[-1] + (sd2[-1]-sd1[-1])/2, 'at risk of\noverweight', verticalalignment='center', fontsize=9)
    plt.text(60.5, sd_minus2[-1] + (sd1[-1]-sd_minus2[-1])/2, 'normal', verticalalignment='center', fontsize=9)
    plt.text(60.5, sd_minus3[-1] + (sd_minus2[-1]-sd_minus3[-1])/2 + 0.5, 'underweight', verticalalignment='center', fontsize=9)
    
    plt.tight_layout()
    plt.subplots_adjust(right=0.85)
    
    # Ensure directory exists
    os.makedirs(os.path.dirname(output_path), exist_ok=True)
    plt.savefig(output_path, dpi=300)
    plt.close()

if __name__ == "__main__":
    dataset_path = r"d:\deteksianakstanding\stunting_app\ml\dataset\stunting_wasting_dataset.csv"
    df = pd.read_csv(dataset_path)
    
    laravel_public_dir = r"c:\Users\Mupin\Downloads\Si Anting\public\images"
    
    # Boys
    boys_out = os.path.join(laravel_public_dir, "bmi-boys.png")
    plot_bmi_chart(df, "l", "BMI-for-Age (BOYS)", boys_out)
    print(f"Berhasil membuat grafik Laki-laki: {boys_out}")
    
    # Girls
    girls_out = os.path.join(laravel_public_dir, "bmi-girls.png")
    plot_bmi_chart(df, "p", "BMI-for-Age (GIRLS)", girls_out)
    print(f"Berhasil membuat grafik Perempuan: {girls_out}")
