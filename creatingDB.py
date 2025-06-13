import os
import gzip
import xml.etree.ElementTree as ET
import pandas as pd

# קבצי הרשתות
uploaded_files = {
    "אושר עד": "pricefiles/PriceFull7290103152017-025-202504160800.gz",
    "חצי חינם": "pricefiles/PriceFull7290700100008-000-201-20250416-004002.gz",
    "יוחננוף": "pricefiles/יוחננוף - רשימת מוצרים מלאה.gz",
    "רמי לוי": "pricefiles/רמי לוי - רשימת מוצרים מלאה.gz",
    "שופרסל": "pricefiles/שופרסל - רשימת מוצרים מלאה.gz"
}

# קובץ עם 200 מוצרים שחופפים לכולן
df_codes = pd.read_excel("pricefiles/real_200_common_products.xlsx")
selected_codes = set(df_codes["קוד מוצר"].astype(str))

# מבנה נתונים
products_data = {}
all_chains = list(uploaded_files.keys())

# סריקה של כל קבצי המחיר
for chain_name, path in uploaded_files.items():
    with gzip.open(path, "rb") as f:
        tree = ET.parse(f)
        root = tree.getroot()
        for item in root.findall(".//Item"):
            code = item.findtext("ItemCode", "").strip()
            if code not in selected_codes:
                continue

            name = item.findtext("ItemName", "").strip()
            brand = item.findtext("ManufacturerName", "").strip()
            price = item.findtext("ItemPrice")
            discount = item.findtext("ItemPriceAfterDiscount")

            if code not in products_data:
                products_data[code] = {
                    "קוד מוצר": code,
                    "שם מוצר": name,
                    "מותג": brand
                }
                for ch in all_chains:
                    products_data[code][f"{ch} – מחיר"] = None
                    products_data[code][f"{ch} – אחרי הנחה"] = None

            products_data[code][f"{chain_name} – מחיר"] = float(price) if price else None
            products_data[code][f"{chain_name} – אחרי הנחה"] = float(discount) if discount else None

# יצירת דאטהפריים
df = pd.DataFrame(list(products_data.values()))

# שמירה לקובץ אקסל
df.to_excel("pricefiles/price_comparison_200_products.xlsx", index=False)
print("✅ הקובץ נוצר: pricefiles/price_comparison_200_products.xlsx")
