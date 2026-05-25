import math

class ZScoreService:
    @staticmethod
    def calculate_zscore(measurement_value: float, l: float, m: float, s: float) -> float:
        """
        Calculates WHO Z-score using LMS formula.
        Z = (((y/M)**L) - 1) / (L*S)
        If L == 0, Z = ln(y/M) / S
        """
        if l == 0:
            return math.log(measurement_value / m) / s
        else:
            return (((measurement_value / m) ** l) - 1) / (l * s)

    @staticmethod
    def classify_stunting(haz_zscore: float) -> str:
        """Height-for-Age classification (Stunting)"""
        if haz_zscore < -3.0:
            return "severely_stunted"
        elif haz_zscore < -2.0:
            return "stunted"
        elif haz_zscore > 3.0:
            return "tall"
        else:
            return "normal"

    @staticmethod
    def classify_wasting(whz_zscore: float) -> str:
        """Weight-for-Height classification (Wasting)"""
        if whz_zscore < -3.0:
            return "severely_wasted"
        elif whz_zscore < -2.0:
            return "wasted"
        elif whz_zscore > 3.0:
            return "obese"
        elif whz_zscore > 2.0:
            return "overweight"
        elif whz_zscore > 1.0:
            return "risk_of_overweight"
        else:
            return "normal"

    @staticmethod
    def classify_underweight(waz_zscore: float) -> str:
        """Weight-for-Age classification (Underweight)"""
        if waz_zscore < -3.0:
            return "severely_underweight"
        elif waz_zscore < -2.0:
            return "underweight"
        else:
            return "normal"
