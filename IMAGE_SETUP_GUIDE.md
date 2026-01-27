# Image Setup Guide for University Academic Portal

## ✅ What Has Been Set Up

All pages have been enhanced with:
- ✅ Consistent image slider templates
- ✅ Visual feature cards
- ✅ Improved visual hierarchy
- ✅ Image directory structure
- ✅ Easy-to-use image placeholders
- ✅ **NEW: Image Card Sections** - Single or double image cards on all guest pages

## 📁 Image Directory Structure

```
public/images/
├── home/          # Home page images (slide4.png, slide5.png, slide6.png)
├── courses/       # Courses page images (slide1.png, slide2.png, slide3.png)
├── news/          # News page images (slide1.png, slide2.png, slide3.png)
├── about/         # About page images (slide1.png, slide2.png, slide3.png)
├── services/      # Services page images (slide1.png, slide2.png, slide3.png)
└── support/       # Support page images (slide1.png, slide2.png, slide3.png)
```

## 🖼️ How to Add Your Images

### 🆕 Image Card Sections (NEW!)

Each guest page now includes a customizable image card section right after the quick links strip. You can add 1 or 2 images per page.

**Location**: `resources/views/guest/partials/image-card.blade.php`

**Quick Setup**:
1. Open `resources/views/guest/partials/image-card.blade.php`
2. Replace `images/home/custom_image_1.jpg` with your image path
3. For a second image, uncomment the "Two Image Cards" section and update `custom_image_2.jpg`
4. Images will appear on: Home, Courses, News, About Us, Academic Services, Support, and Contact Us pages

**Image Recommendations for Cards**:
- **Format**: PNG or JPG
- **Recommended Size**: 1200×675 pixels (16:9 aspect ratio)
- **File Size**: Keep under 300KB for optimal loading
- **Placement**: Add images to `public/images/home/` or create page-specific folders

**Example Paths**:
- `images/home/campus_photo.jpg`
- `images/courses/featured_course.jpg`
- `images/about/university_building.jpg`

---

### Step 1: Prepare Your Images
- **Format**: PNG or JPG
- **Recommended Size**: 1920×640 pixels (16:5.33 aspect ratio)
- **Minimum Size**: 1200×400 pixels
- **File Size**: Keep under 500KB for optimal loading
- **Quality**: Use high quality but optimize for web

### Step 2: Add Images to the Correct Directory

#### For Home Page (Add 1-3 images):
Place your images in `public/images/home/`:
- `slide4.png` - Your first custom image
- `slide5.png` - Your second custom image (optional)
- `slide6.png` - Your third custom image (optional)

#### For Courses Page:
Place your images in `public/images/courses/`:
- `slide1.png` - Featured programs image
- `slide2.png` - Faculty & mentors image
- `slide3.png` - Labs & learning spaces image

#### For News Page:
Place your images in `public/images/news/`:
- `slide1.png` - University announcements image
- `slide2.png` - Events & activities image
- `slide3.png` - Research & innovation image

#### For About Page:
Place your images in `public/images/about/`:
- `slide1.png` - Campus life image
- `slide2.png` - Leadership & vision image
- `slide3.png` - Milestones & achievements image

#### For Services Page:
Place your images in `public/images/services/`:
- `slide1.png` - Registration & courses image
- `slide2.png` - Grades, fees, attendance image
- `slide3.png` - Timetables & notifications image

#### For Support Page:
Place your images in `public/images/support/`:
- `slide1.png` - FAQs & guides image
- `slide2.png` - Issue reporting image
- `slide3.png` - Contact channels image

### Step 3: Update Image Titles and Captions (Optional)

After adding your images, you can update the titles and captions in the Blade templates:

- **Home Page**: `resources/views/guest/home.blade.php` (lines 195-234)
- **Courses Page**: `resources/views/guest/courses.blade.php` (lines 99-137)
- **News Page**: `resources/views/guest/news.blade.php` (lines 103-141)
- **About Page**: `resources/views/guest/about.blade.php` (lines 86-124)
- **Services Page**: `resources/views/guest/services.blade.php` (lines 64-102)
- **Support Page**: `resources/views/guest/support.blade.php` (lines 58-96)

## 🎨 Visual Enhancements Added

### All Pages Now Include:
1. **Hero Sliders** - Image carousels with navigation dots
2. **Visual Feature Cards** - Colorful cards highlighting key features
3. **Improved Typography** - Better text hierarchy and readability
4. **Hover Effects** - Interactive elements that respond to user interaction
5. **Gradient Overlays** - Professional image overlays for better text readability
6. **Responsive Design** - All images and layouts work on mobile, tablet, and desktop

### Specific Enhancements:

#### Home Page:
- Enhanced hero section with animated background
- Image slider with 6 slides (3 existing + 3 customizable)
- Statistics cards with icons
- Testimonials section
- Featured courses preview
- Latest announcements preview

#### Courses Page:
- Visual feature cards (Diverse Programs, Expert Faculty, Career Ready)
- Enhanced course cards with hover effects
- Search and filter functionality
- Statistics overview

#### News Page:
- Visual feature cards (Urgent Updates, Campus Events, Research News)
- Priority-based announcement cards
- Search and filter functionality
- Statistics overview

#### About Page:
- Mission & Vision cards
- Core Values grid
- Statistics section
- Timeline/Milestones section
- Campus Highlights grid

#### Services Page:
- Service cards with statistics
- Visual slider showcasing services
- Detailed service descriptions

#### Support Page:
- Support option cards
- Visual slider for help desk highlights
- Contact information cards

## 💡 Tips for Best Results

1. **Consistency**: Use similar styling and color schemes across all images
2. **Text Overlays**: Ensure any text in images is readable with the gradient overlay
3. **Optimization**: Compress images before uploading to improve page load speed
4. **Aspect Ratio**: Maintain the recommended 1920×640px aspect ratio for best display
5. **File Naming**: Use the exact filenames specified (e.g., slide1.png, slide2.png)
6. **Testing**: After adding images, test on different screen sizes to ensure they display correctly

## 🔧 Troubleshooting

### Images Not Showing?
1. Check file paths match exactly (case-sensitive)
2. Ensure images are in the correct directory
3. Clear browser cache
4. Check file permissions

### Images Look Stretched?
- Ensure images are 1920×640px or maintain the same aspect ratio
- Check that `background-size: cover` is applied (already in code)

### Want to Remove a Slide?
- Simply delete or rename the image file
- Remove the corresponding slide div in the Blade template
- Remove the corresponding navigation dot

## 📝 Notes

- All image paths use Laravel's `asset()` helper for proper URL generation
- Images are automatically optimized with CSS for better performance
- The slider includes autoplay functionality (6-second intervals)
- All sliders are fully responsive and touch-enabled
- **Image Card sections** show a placeholder if the image file is not found
- You can customize each page's image card independently by editing the partial file

## 🎯 Image Card Customization

The image card section supports:
- **Single Image**: One full-width image card (default)
- **Two Images**: Side-by-side layout (uncomment the second section)
- **Hover Effects**: Images scale slightly on hover
- **Responsive**: Automatically adjusts for mobile, tablet, and desktop
- **Placeholder**: Shows a helpful message if image is missing

To customize per page, you can:
1. Create page-specific image card partials (e.g., `image-card-home.blade.php`)
2. Or modify the main partial to use different images based on the current route

For more details, see `public/images/README.md`
