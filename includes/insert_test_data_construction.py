import sqlite3
from datetime import datetime

# データベースパス
db_path = '/home/user/webapp/data/izuso_portal.db'

try:
    conn = sqlite3.connect(db_path)
    cursor = conn.cursor()
    
    # テストデータを挿入
    test_data = {
        'order_number': 'K20260316-0001',
        'chome_ban': '2丁目5-8',
        'owner_name': '佐藤花子',
        'construction_company': '静岡建設',
        'water_company': '伊豆水道サービス',
        'pipe_diameter': '25mm',
        'water_application_date': '2026-03-12',
        'payment_date': '2026-03-14',
        'amount': 200000,
        'neighborhood_association': '東町自治会',
        'construction_date': '2026-03-22',
        'notes': 'テスト用データ - 工事受付表新規仕様確認用'
    }
    
    cursor.execute("""
        INSERT INTO construction_orders (
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
