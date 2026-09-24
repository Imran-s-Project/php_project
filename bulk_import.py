#!/usr/bin/env python3
"""
Bulk question importer for the Exam Website.

Reads questions from an Excel (.xlsx) or CSV file and inserts them
into the `questions` table for a given exam.

Expected columns (header row required), in any order:
    question_text, option_a, option_b, option_c, option_d,
    correct_option, explanation (optional), marks (optional, default 1)

Usage:
    python bulk_import.py --exam-id 1 --file questions.xlsx
    python bulk_import.py --exam-id 1 --file questions.csv

Install dependencies first:
    pip install -r requirements.txt
"""

import argparse
import csv
import sys

import mysql.connector
import pandas as pd

DB_CONFIG = {
    "host": "localhost",
    "user": "root",
    "password": "",
    "database": "exam_system",
}

REQUIRED_COLUMNS = [
    "question_text", "option_a", "option_b", "option_c", "option_d", "correct_option",
]


def read_rows(file_path):
    if file_path.lower().endswith(".csv"):
        with open(file_path, newline="", encoding="utf-8-sig") as f:
            return list(csv.DictReader(f))
    df = pd.read_excel(file_path, dtype=str).fillna("")
    return df.to_dict(orient="records")


def validate_row(row, line_number):
    missing = [col for col in REQUIRED_COLUMNS if not str(row.get(col, "")).strip()]
    if missing:
        raise ValueError("সারি {}: এই কলামগুলো খালি — {}".format(line_number, ", ".join(missing)))
    correct = str(row["correct_option"]).strip().upper()
    if correct not in ("A", "B", "C", "D"):
        raise ValueError("সারি {}: correct_option অবশ্যই A/B/C/D হতে হবে, পাওয়া গেছে '{}'".format(line_number, correct))


def main():
    parser = argparse.ArgumentParser(description="Bulk import exam questions from Excel/CSV into MySQL.")
    parser.add_argument("--exam-id", type=int, required=True, help="যে exam এ প্রশ্ন যোগ হবে তার ID")
    parser.add_argument("--file", type=str, required=True, help="Excel (.xlsx) অথবা CSV ফাইলের পাথ")
    args = parser.parse_args()

    try:
        rows = read_rows(args.file)
    except FileNotFoundError:
        print("ফাইল পাওয়া যায়নি: {}".format(args.file))
        sys.exit(1)

    if not rows:
        print("ফাইলে কোনো ডেটা পাওয়া যায়নি।")
        sys.exit(1)

    try:
        for i, row in enumerate(rows, start=2):  # header = row 1
            validate_row(row, i)
    except ValueError as err:
        print(str(err))
        sys.exit(1)

    try:
        conn = mysql.connector.connect(**DB_CONFIG)
    except mysql.connector.Error as err:
        print("ডাটাবেসে সংযোগ ব্যর্থ হয়েছে: {}".format(err))
        print("bulk_import.py ফাইলের উপরে DB_CONFIG আপডেট করুন।")
        sys.exit(1)

    cursor = conn.cursor()
    cursor.execute("SELECT id FROM exams WHERE id = %s", (args.exam_id,))
    if cursor.fetchone() is None:
        print("exam_id {} খুঁজে পাওয়া যায়নি।".format(args.exam_id))
        sys.exit(1)

    insert_sql = """
        INSERT INTO questions
            (exam_id, question_text, option_a, option_b, option_c, option_d, correct_option, explanation, marks)
        VALUES (%s, %s, %s, %s, %s, %s, %s, %s, %s)
    """

    inserted = 0
    for row in rows:
        marks = row.get("marks") or 1
        cursor.execute(insert_sql, (
            args.exam_id,
            str(row["question_text"]).strip(),
            str(row["option_a"]).strip(),
            str(row["option_b"]).strip(),
            str(row["option_c"]).strip(),
            str(row["option_d"]).strip(),
            str(row["correct_option"]).strip().upper(),
            str(row.get("explanation", "")).strip(),
            int(marks),
        ))
        inserted += 1

    conn.commit()
    cursor.close()
    conn.close()

    print("সম্পন্ন — {} টি প্রশ্ন exam_id {} এ যোগ করা হয়েছে।".format(inserted, args.exam_id))


if __name__ == "__main__":
    main()
