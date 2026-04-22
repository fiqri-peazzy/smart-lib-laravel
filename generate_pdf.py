#!/usr/bin/env python3
"""
Script untuk generate PDF dokumentasi SIPERPUS Fasilkom
Menggunakan WeasyPrint untuk convert HTML to PDF
"""

import os
import sys

# Try install weasyprint if not available
try:
    from weasyprint import HTML, CSS
except ImportError:
    print("Installing weasyprint...")
    os.system("pip install weasyprint")
    from weasyprint import HTML, CSS

def generate_pdf():
    """Generate PDF dari HTML file"""
    
    input_html = r"c:\xampp\htdocs\smart-lib\SMART_LIB_DOCUMENTATION.html"
    output_pdf = r"c:\xampp\htdocs\smart-lib\SIPERPUS_Fasilkom_Documentation_v1.0.pdf"
    
    print("📄 Membaca file HTML...")
    if not os.path.exists(input_html):
        print(f"❌ File tidak ditemukan: {input_html}")
        return False
    
    print(f"🔄 Converting HTML ke PDF...")
    try:
        # Convert HTML to PDF
        HTML(filename=input_html).write_pdf(output_pdf)
        
        # Check file size
        file_size = os.path.getsize(output_pdf) / (1024 * 1024)  # Convert to MB
        
        print(f"✅ PDF berhasil dibuat!")
        print(f"📁 File: {output_pdf}")
        print(f"📊 Ukuran: {file_size:.2f} MB")
        return True
        
    except Exception as e:
        print(f"❌ Error: {str(e)}")
        return False

if __name__ == "__main__":
    generate_pdf()
