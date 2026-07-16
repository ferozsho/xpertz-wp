#!/usr/bin/env python3
"""
Download free stock photos for LearnPress courses and set them as featured images
via WordPress REST API.
"""

import os
import sys
import json
import tempfile
import requests
from PIL import Image
from io import BytesIO

# WordPress site info
SITE_URL = "http://localhost:9080"
WORDPRESS_ADMIN_USER = "admin1"
WORDPRESS_ADMIN_PASS = "admin1"

# Courses data
COURSES = [
    {"id": 61, "slug": "learnpress-course-1", "title": "LearnPress Course 1", "query": "online learning education"},
    {"id": 99, "slug": "learnpress-course-2", "title": "LearnPress Course 2", "query": "student studying library"},
    {"id": 140, "slug": "learnpress-course-3", "title": "LearnPress Course 3", "query": "classroom teaching"},
    {"id": 172, "slug": "learnpress-course-4", "title": "LearnPress Course 4", "query": "graduation ceremony"},
    {"id": 206, "slug": "learnpress-course-5", "title": "LearnPress Course 5", "query": "laptop learning technology"},
    {"id": 246, "slug": "learnpress-course-6", "title": "LearnPress Course 6", "query": "books knowledge"},
    {"id": 278, "slug": "learnpress-course-7", "title": "LearnPress Course 7", "query": "science lab research"},
    {"id": 311, "slug": "learnpress-course-8", "title": "LearnPress Course 8", "query": "presentation workshop"},
    {"id": 346, "slug": "learnpress-course-9", "title": "LearnPress Course 9", "query": "writing notes study"},
    {"id": 382, "slug": "learnpress-course-10", "title": "LearnPress Course 10", "query": "global education world"},
]

# Target image dimensions
IMG_WIDTH = 1200
IMG_HEIGHT = 800

# Output directory for downloaded images
OUTPUT_DIR = "/home/administrator/xpertz-wp/uploads/course-images"

# Pexels API - free to use without key for basic access
PEXELS_API_KEY = "563492ad6f91700001000001e628f0e108fe44b483af16995e42332f"  # Demo key
PEXELS_API_URL = "https://api.pexels.com/v1/search"


def download_image(url, timeout=30):
    """Download an image from URL."""
    headers = {
        "User-Agent": "Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36"
    }
    resp = requests.get(url, headers=headers, timeout=timeout)
    resp.raise_for_status()
    return resp.content


def search_pexels_image(query):
    """Search for an image on Pexels."""
    headers = {"Authorization": PEXELS_API_KEY}
    params = {
        "query": query,
        "per_page": 1,
        "orientation": "landscape",
        "size": "large",
    }
    try:
        resp = requests.get(PEXELS_API_URL, headers=headers, params=params, timeout=15)
        if resp.status_code == 200:
            data = resp.json()
            if data.get("photos"):
                return data["photos"][0]["src"]["large"]
    except Exception as e:
        print(f"  Pexels search failed: {e}")
    return None


def fetch_picsum_image(seed):
    """Fallback: fetch a random image from picsum.photos (free, no API key)."""
    url = f"https://picsum.photos/seed/{seed}/{IMG_WIDTH}/{IMG_HEIGHT}"
    try:
        return download_image(url)
    except Exception:
        return None


def process_image(image_data, output_path):
    """Process image with Pillow: resize and optimize."""
    img = Image.open(BytesIO(image_data))

    # Convert to RGB if needed
    if img.mode in ("RGBA", "P"):
        img = img.convert("RGB")

    # Resize to target dimensions (cover crop)
    img_ratio = img.width / img.height
    target_ratio = IMG_WIDTH / IMG_HEIGHT

    if img_ratio > target_ratio:
        # Image is wider - crop width
        new_height = img.height
        new_width = int(new_height * target_ratio)
        left = (img.width - new_width) // 2
        img = img.crop((left, 0, left + new_width, new_height))
    else:
        # Image is taller - crop height
        new_width = img.width
        new_height = int(new_width / target_ratio)
        top = (img.height - new_height) // 2
        img = img.crop((0, top, new_width, top + new_height))

    # Resize to exact dimensions
    img = img.resize((IMG_WIDTH, IMG_HEIGHT), Image.LANCZOS)

    # Save optimized
    img.save(output_path, "JPEG", quality=85, optimize=True)
    print(f"  Saved: {output_path} ({os.path.getsize(output_path)} bytes)")


def upload_to_wordpress(image_path, course_title):
    """Upload image to WordPress media library via REST API and return attachment ID."""
    import mimetypes

    filename = os.path.basename(image_path)
    mime_type = mimetypes.guess_type(image_path)[0] or "image/jpeg"

    # Step 1: Get nonce and cookie for authentication
    session = requests.Session()

    # Login to WordPress
    login_url = f"{SITE_URL}/wp-login.php"
    login_data = {
        "log": WORDPRESS_ADMIN_USER,
        "pwd": WORDPRESS_ADMIN_PASS,
        "wp-submit": "Log In",
        "redirect_to": f"{SITE_URL}/wp-admin/",
        "testcookie": "1",
    }
    session.post(login_url, data=login_data, timeout=15)

    # Step 2: Upload via admin-post or rest API
    with open(image_path, "rb") as f:
        files = {
            "async-upload": (filename, f, mime_type),
        }
        data = {
            "action": "upload-attachment",
            "name": filename,
            "_ajax_nonce": "",  # Will get from page
        }

        # Get the upload nonce from media-new.php page
        media_page = session.get(f"{SITE_URL}/wp-admin/media-new.php", timeout=15)

        # Extract nonce from page
        import re
        nonce_match = re.search(r'name="_wpnonce" value="([^"]+)"', media_page.text)
        if nonce_match:
            data["_wpnonce"] = nonce_match.group(1)

        # Upload via async-upload
        upload_url = f"{SITE_URL}/wp-admin/async-upload.php"
        resp = session.post(upload_url, data=data, files=files, timeout=30)

        if resp.status_code == 200:
            # Parse response for attachment ID
            id_match = re.search(r'id=["\'](\d+)["\']', resp.text)
            if id_match:
                attachment_id = int(id_match.group(1))
                print(f"  Uploaded to WordPress media library: attachment ID {attachment_id}")
                return attachment_id
            else:
                print(f"  Upload response: {resp.text[:300]}")
        else:
            print(f"  Upload failed: {resp.status_code}")

    return None


def set_course_thumbnail(course_id, attachment_id):
    """Set the featured image for a course via WordPress REST API."""
    session = requests.Session()

    # Login
    login_url = f"{SITE_URL}/wp-login.php"
    login_data = {
        "log": WORDPRESS_ADMIN_USER,
        "pwd": WORDPRESS_ADMIN_PASS,
        "wp-submit": "Log In",
        "redirect_to": f"{SITE_URL}/wp-admin/",
        "testcookie": "1",
    }
    session.post(login_url, data=login_data, timeout=15)

    # Get REST nonce
    admin_page = session.get(f"{SITE_URL}/wp-admin/admin-ajax.php?action=rest-nonce", timeout=15)
    nonce = admin_page.text.strip() if admin_page.text else ""

    # Set thumbnail via REST API
    rest_url = f"{SITE_URL}/wp-json/wp/v2/lp_course/{course_id}"
    headers = {
        "X-WP-Nonce": nonce,
        "Content-Type": "application/json",
    }
    data = {
        "featured_media": attachment_id,
    }

    resp = session.post(rest_url, headers=headers, json=data, timeout=15)
    if resp.status_code in (200, 201):
        print(f"  ✓ Featured image set for course ID {course_id}")
        return True
    else:
        print(f"  ✗ Failed to set featured image: {resp.status_code} {resp.text[:200]}")
        return False


def main():
    os.makedirs(OUTPUT_DIR, exist_ok=True)

    success_count = 0

    for course in COURSES:
        print(f"\n{'='*60}")
        print(f"Processing: {course['title']} (ID: {course['id']})")
        print(f"{'='*60}")

        # Try Pexels first
        image_url = search_pexels_image(course["query"])

        if image_url:
            print(f"  Found image on Pexels: {image_url[:80]}...")
            try:
                image_data = download_image(image_url)
            except Exception as e:
                print(f"  Failed to download from Pexels: {e}")
                image_data = None
        else:
            image_data = None

        # Fallback to picsum
        if not image_data:
            print(f"  Falling back to picsum.photos...")
            image_data = fetch_picsum_image(course["slug"])

        if not image_data:
            print(f"  ✗ Failed to get any image for {course['title']}")
            continue

        # Process with Pillow
        output_path = os.path.join(OUTPUT_DIR, f"{course['slug']}.jpg")
        process_image(image_data, output_path)

        # Upload to WordPress
        attachment_id = upload_to_wordpress(output_path, course["title"])
        if attachment_id:
            # Set as course thumbnail
            if set_course_thumbnail(course["id"], attachment_id):
                success_count += 1

    print(f"\n{'='*60}")
    print(f"Done! Successfully set images for {success_count}/{len(COURSES)} courses.")
    print(f"{'='*60}")


if __name__ == "__main__":
    main()
