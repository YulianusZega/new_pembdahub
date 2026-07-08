# SISFOPEMBDA - Sistem Informasi Administrasi Pembayaran Gaji
## Final Production Structure

### 📁 Core System Files
- **config.php** - Database configuration and connection
- **auth.php** - Authentication and authorization system
- **index.php** - Application entry point (redirects to login)
- **login.php** - User login interface
- **dashboard.php** - Main dashboard with statistics

### 📁 Main Modules
- **input_pegawai.php** - Employee data management
- **input_unit.php** - Unit/department management
- **input_jabatan.php** - Position management
- **input_jam_honor.php** - Honor hour rules management
- **input_penugasan.php** - Assignment and salary calculation
- **jabatan.php** - Position master data
- **unit.php** - Unit master data
- **users.php** - User management
- **tunjangan_formula.php** - Allowance formula management

### 📁 Reports Module
- **laporan.php** - Assignment reports
- **laporan_gaji.php** - Salary report interface
- **generate_salary_report.php** - Salary report generation (Excel/CSV/PDF)
- **slip_gaji.php** - Individual salary slip

### 📁 AJAX Endpoints
- **get_calculation_preview.php** - Real-time salary calculation preview
- **get_dashboard_data.php** - Dashboard statistics data
- **get_detail_pegawai.php** - Employee detail information
- **get_gaji_detail.php** - Detailed salary breakdown
- **get_pegawai_data.php** - Employee data for forms
- **csv_export.php** - CSV export functionality

### 📁 Business Logic
- **override_functions.php** - Salary calculation with override rules
- **override_rules.php** - Override rules management interface

### 📁 Assets & Resources
- **assets/** - CSS, JS, images and other static resources
- **pegawai.js** - JavaScript for employee-related functionality
- **README.md** - Project documentation
- **database.sql** - Database schema
- **sisfopembda.sql** - Full database backup

## 🚀 Features Overview

### Core Features
✅ **Employee Management** - Full CRUD for employee data
✅ **Position Management** - Job positions with allowances
✅ **Unit Management** - Organizational units
✅ **Assignment Management** - Employee assignments with salary calculation
✅ **User Management** - Multi-user access with roles

### Salary System
✅ **Automatic Calculation** - Real-time salary calculation
✅ **Allowance System** - Family, child, and rice allowances
✅ **Override Rules** - Custom rules for specific employees
✅ **Honor Hours** - Teaching hour calculations
✅ **Status-based Rules** - Different rules for GTY, PTY, PNS, etc.

### Reporting
✅ **Comprehensive Reports** - Detailed salary reports
✅ **Multiple Export Formats** - Excel, CSV, PDF
✅ **Individual Slip Gaji** - Personal salary slips
✅ **Group by Unit** - Organized by departments
✅ **Rekapitulasi** - Summary totals and grand totals

### UI/UX
✅ **Modern Bootstrap Design** - Professional interface
✅ **Responsive Layout** - Mobile-friendly
✅ **Interactive Elements** - Modals, tooltips, real-time updates
✅ **Status Color Coding** - Visual status indicators
✅ **Beautiful Export Buttons** - Gradient buttons with animations

## 🛠️ Technical Stack
- **Backend**: PHP 7.4+
- **Database**: MySQL/MariaDB
- **Frontend**: Bootstrap 5.1.3, jQuery, Font Awesome
- **Export**: HTML tables for Excel compatibility
- **Authentication**: Session-based with role management

## 📊 Database Tables
- `pegawai` - Employee master data
- `unit` - Organizational units
- `jabatan` - Job positions
- `jam_honor` - Honor hour rules
- `penugasan` - Employee assignments
- `penugasan_jabatan` - Assignment-position relationships
- `tunjangan_formula` - Allowance formulas
- `pegawai_override_rules` - Custom salary rules
- `users` - System users

## 🎯 System Capabilities
- **Multi-user Access** with role-based permissions
- **Real-time Calculations** with AJAX
- **Hierarchical Ordering** by position priority
- **Override System** for custom salary rules
- **Professional Reports** with proper formatting
- **Export Capabilities** in multiple formats
- **Responsive Design** for all devices

## 🔧 Installation
1. Upload files to web server
2. Import `sisfopembda.sql` to MySQL database
3. Configure `config.php` with database credentials
4. Access application via web browser
5. Default login: admin/admin

## 📝 Total Files: 31
**Production-ready** - All development and test files removed
**Optimized** - Only essential files for operation
**Clean** - No backup or temporary files
**Professional** - Ready for deployment

---
**Developed by**: SISFOPEMBDA Team
**Version**: 1.0 Production
**Date**: August 2025
