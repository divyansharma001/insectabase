# 🦋 InsectaBase Cloudinary Integration

This document explains how to set up Cloudinary integration for InsectaBase to handle image storage and PDF downloads.

## 🚀 Quick Setup

1. **Install Dependencies**
   ```bash
   composer install
   ```

2. **Run Setup Script**
   ```bash
   php setup_cloudinary.php
   ```

3. **Configure Cloudinary**
   - Sign up at [cloudinary.com](https://cloudinary.com)
   - Get your credentials from the dashboard
   - Update `.env` file with your credentials

## 📋 Features

### ✅ What's Included

- **Image Storage**: High-quality insect photos stored on Cloudinary
- **PDF Downloads**: Research papers and field guides available for download
- **Optimized Delivery**: Automatic image optimization and format conversion
- **Sample Data**: Beautiful demo content with realistic scientific data
- **Real Research Links**: Links to actual scientific publications

### 🖼️ Image Integration

- **Picture of the Day**: Random selection from 6 high-quality moth images
- **Background Images**: Dynamic backgrounds using Cloudinary URLs
- **Optimized Loading**: Automatic quality and format optimization

### 📚 PDF Integration

- **Research Papers**: Downloadable PDFs of scientific studies
- **Field Guides**: Complete guides to Tortricidae identification
- **Conservation Reports**: Status reports on endangered species
- **Taxonomic Studies**: Molecular phylogeny research papers

## 🔧 Configuration

### Environment Variables

```env
# Cloudinary Configuration
CLOUDINARY_CLOUD_NAME=your_cloud_name
CLOUDINARY_API_KEY=your_api_key
CLOUDINARY_API_SECRET=your_api_secret
```

### Cloudinary Settings

1. **Enable PDF Delivery**
   - Go to Settings > Security
   - Check "Allow delivery of PDF and ZIP files"
   - Save changes

2. **Upload Your Content**
   - Upload images to `insectabase/images/` folder
   - Upload PDFs to `insectabase/pdfs/` folder
   - Use the provided upload functions in `includes/cloudinary.php`

## 📁 File Structure

```
includes/
├── cloudinary.php          # Cloudinary configuration and functions
├── db.php                  # Database config with Cloudinary integration
└── ...

assets/
├── img/                    # Local fallback images
└── literature/             # Local PDF fallbacks

setup_cloudinary.php        # Setup script
CLOUDINARY_SETUP.md         # This documentation
```

## 🎨 Sample Content

The application includes beautiful sample content:

### Images
- 6 high-quality Tortricidae moth images
- Scientific names and descriptions
- Optimized for web delivery

### Research Papers
- Molecular phylogeny studies
- Field identification guides
- Conservation status reports
- Taxonomic revisions

### News Articles
- Real scientific publication links
- Research updates and discoveries
- Conservation alerts
- Educational content

## 🔄 Migration from Local Files

To migrate from local files to Cloudinary:

1. **Upload Images**
   ```php
   $imageUrl = uploadImageToCloudinary('path/to/local/image.jpg', 'insectabase/images');
   ```

2. **Upload PDFs**
   ```php
   $pdfUrl = uploadPdfToCloudinary('path/to/local/document.pdf', 'insectabase/pdfs');
   ```

3. **Update Database**
   - Replace local URLs with Cloudinary URLs
   - Use the provided sample data as reference

## 🚀 Benefits

- **Performance**: Faster image loading with CDN
- **Scalability**: Handle large files and high traffic
- **Optimization**: Automatic image compression and format conversion
- **Reliability**: 99.9% uptime with global CDN
- **Security**: Secure file delivery with access controls

## 📞 Support

For issues with Cloudinary integration:
1. Check the setup script output
2. Verify your credentials in `.env`
3. Ensure PDF delivery is enabled in Cloudinary
4. Check the browser console for any errors

The application works with sample data even without Cloudinary configuration, so you can test all features immediately!
