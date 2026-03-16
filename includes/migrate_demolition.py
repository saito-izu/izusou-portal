import sqlite3
import os

# データベースパス
db_path = '/home/user/webapp/data/izuso_portal.db'

try:
    conn = sqlite3.connect(db_path)
    cursor = conn.cursor()
    
    # 既存テーブルのバックアップ
    cursor.execute("DROP TABLE IF EXISTS demolition_orders_backup")
    cursor.execute("CREATE TABLE demolition_orders_backup AS SELECT * FROM demolition_orders")
    
    # 既存テーブルを削除
    cursor.execute("DROP TABLE IF EXISTS demolition_orders")
    
    # 新しいスキーマでテーブルを再作成
    cursor.execute("""
        CREATE TABLE demolition_orders (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            order_number TEXT UNIQUE NOT NULL,
            chome_ban TEXT,
            owner_name TEXT,
            construction_company TEXT,
            water_company TEXT,
            pipe_diameter TEXT,
            water_application_date DATE,
            payment_date DATE,
            amount DECIMAL(15,2),
            neighborhood_association TEXT,
            construction_date DATE,
            notes TEXT,
            created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
            updated_at DATETIME DEFAULT CURRENT_TIMESTAMP
        )
    """)
    
    conn.commit()
    print("マイグレーション完了: demolition_ordersテーブルを新しいスキーマで再作成しました。")
    print("古いデータは demolition_orders_backup テーブルに保存されています。")
    
except Exception as e:
    print(f"マイグレーションエラー: {e}")
    if conn:
        conn.rollback()
finally:
    if conn:
        conn.close()
