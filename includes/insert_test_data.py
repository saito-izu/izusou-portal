import sqlite3
from datetime import datetime

# データベースパス
db_path = '/home/user/webapp/data/izuso_portal.db'

try:
    conn = sqlite3.connect(db_path)
    cursor = conn.cursor()
    
    # テストデータを挿入
    test_data = {
        'order_number': 'D20260316-0001',
        'chome_ban': '1丁目2-3',
        'owner_name': '山田太郎',
        'construction_company': '伊豆建設株式会社',
        'water_company': '伊豆水道工業',
        'pipe_diameter': '20mm',
        'water_application_date': '2026-03-10',
        'payment_date': '2026-03-15',
        'amount': 150000,
        'neighborhood_association': '西町自治会',
        'construction_date': '2026-03-20',
        'notes': 'テスト用データ - 新規仕様確認用'
    }
    
    cursor.execute("""
        INSERT INTO demolition_orders (
            order_number, chome_ban, owner_name, construction_company, 
            water_company, pipe_diameter, water_application_date, 
            payment_date, amount, neighborhood_association, 
            construction_date, notes
        ) VALUES (
            :order_number, :chome_ban, :owner_name, :construction_company,
            :water_company, :pipe_diameter, :water_application_date,
            :payment_date, :amount, :neighborhood_association,
            :construction_date, :notes
        )
    """, test_data)
    
    conn.commit()
    print("テストデータを登録しました。")
    print(f"受付番号: {test_data['order_number']}")
    
except Exception as e:
    print(f"エラー: {e}")
    if conn:
        conn.rollback()
finally:
    if conn:
        conn.close()
