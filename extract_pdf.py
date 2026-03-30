import sys
import io
from pypdf import PdfReader

def extract_text(pdf_path, start_page=83): # 0-indexed, so page 84 is index 83
    try:
        reader = PdfReader(pdf_path)
        text = ""
        # We assume Bai 5 and Bai 6 are within 40 pages from page 84.
        for i in range(start_page, min(start_page + 40, len(reader.pages))):
            text += f"--- Page {i+1} ---\n"
            text += reader.pages[i].extract_text() + "\n"
        
        with io.open("extracted_pages.txt", "w", encoding="utf-8") as f:
            f.write(text)
        print("Success")
    except Exception as e:
        print(f"Error: {e}")

if __name__ == "__main__":
    extract_text(r"c:\laragon\www\thuchanhmanguonmo-main\[COS340] THỰC HÀNH PHÁT TRIỂN PHẦN MỀM MÃ NGUỒN MỞ.pdf")
