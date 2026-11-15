# Industry Atlas — Resource Library

**A modern, responsive WordPress plugin for managing and displaying downloadable manufacturing resources.**

Industry Atlas Resource Library is a custom WordPress plugin developed for **Industry Atlas Group**. It provides an organized, searchable resource library where visitors can explore and download helpful guides, checklists, and educational materials.

The plugin integrates seamlessly with WordPress and Elementor using a simple shortcode.

![Version](https://img.shields.io/badge/Version-2.0.0-0B2E4F)
![Platform](https://img.shields.io/badge/Platform-WordPress-21759B)
![PHP](https://img.shields.io/badge/Language-PHP-777BB4)
![Elementor](https://img.shields.io/badge/Elementor-Compatible-92003B)

## Live Design Preview

[View Resource Library Screenshot](https://prnt.sc/0rWTR4OVDfgW)

## Project Overview

The Resource Library helps manufacturers, suppliers, industrial service providers, and business professionals access useful industry knowledge and downloadable resources.

It supports Industry Atlas Group's mission:

**Helping Manufacturers Get Found, Get Certified & Get Connected™**

The plugin can be embedded into an existing WordPress page without changing the website's header, footer, or theme.

## Key Features

- **Responsive Resource Library:** Professional card-based layout optimized for desktop, tablet, and mobile devices.
- **WordPress Admin Dashboard:** Add, edit, publish, and manage downloadable resources.
- **Downloadable Resources:** Upload and share PDF, DOCX, and other WordPress-supported file formats.
- **Resource Categories:** Organize materials into relevant industry categories.
- **Live Search:** Help visitors find resources by searching titles and descriptions.
- **Category Filtering:** Allow users to browse resources based on their interests.
- **Load More Functionality:** Display additional resources without reloading the page.
- **Elementor Integration:** Easily embed the library using a Shortcode Widget.
- **Sample Resource:** Includes a sample manufacturing visibility checklist for demonstration.
- **Custom Styling:** Professional navy blue and white design matching the Industry Atlas Group brand.

## Technology Stack

| Technology | Purpose |
|---|---|
| WordPress | Content Management System |
| PHP | Backend functionality |
| JavaScript | Search, filtering, and interactive features |
| CSS3 | Responsive design and visual styling |
| WordPress Custom Post Types | Resource management |
| WordPress Media Library | File management |
| Elementor | Shortcode-based page integration |

## Installation Guide

**Step 1 — Download the Plugin**

Download the source files from this repository and prepare the correct WordPress plugin directory structure.

**Step 2 — Install in WordPress**

Navigate to:

`WordPress Dashboard → Plugins → Add New Plugin → Upload Plugin`

Upload the installable plugin ZIP file.

**Step 3 — Activate**

Click **Activate Plugin**.

**Step 4 — Add the Shortcode**

Open your desired page using Elementor or the WordPress Block Editor and insert:

`[iag_resource_library]`

**Step 5 — Publish**

Save or publish the page. The Resource Library section will appear in the selected location.

**Note:** The plugin does not automatically create a new WordPress page. You can add the library to any existing or new page using its shortcode.

## Shortcode Usage

**Default Resource Library**

`[iag_resource_library]`

**Custom Section Title**

`[iag_resource_library title="Downloads & Guides"]`

**Custom Initial Resource Limit**

`[iag_resource_library limit="9"]`

These options let administrators customize how the Resource Library appears on different pages.

## Resource Management

The plugin provides a dedicated **Resource Library** menu inside the WordPress Dashboard.

### Adding a New Resource

1. Open **WordPress Dashboard → Resource Library → Add Resource**.
2. Enter the resource title and description.
3. Select the appropriate resource category.
4. Upload or select a downloadable file.
5. Publish the resource.

Once published, the resource appears automatically in the frontend library.

### Managing Existing Resources

Administrators can update resource titles, descriptions, categories, and downloadable files at any time.

Resources can also be unpublished or deleted from the WordPress Dashboard.

## Resource Categories

The initial resource categories include:

- **Manufacturing Visibility:** Guides and checklists for improving business visibility.
- **Supplier Discovery:** Educational resources for identifying suppliers and industrial partners.
- **Certification & Compliance:** Materials relating to certification readiness and operational standards.

Additional categories can be created and managed from the WordPress Dashboard.

## Recommended File Structure

The plugin source should follow this structure:

```text
industry-atlas-resource-library/
├── industry-atlas-resource-library.php
├── assets/
│   ├── admin.js
│   ├── resource-library.css
│   ├── resource-library.js
│   └── sample-manufacturing-visibility-checklist.pdf
├── README-INSTALL.txt
└── README.md
```

**Important:** Keep the CSS, JavaScript, and sample PDF files inside the `assets` directory as expected by the plugin's PHP code.

## Design System

The frontend styling follows the Industry Atlas Group brand identity.

| Element | Color |
|---|---|
| Primary Navy Blue | `#0B2E4F` |
| Steel Gray | `#5D6D7E` |
| White Background | `#FFFFFF` |
| Light Gray Background | `#F5F7FA` |

The layout is designed to provide a clean, modern, professional experience appropriate for industrial and manufacturing organizations.

## Troubleshooting

**Resource Library is not appearing**

Verify that the plugin is activated and the correct shortcode has been inserted.

**Styles are not loading**

Confirm that the plugin's CSS file exists inside the `assets` directory. Clear your WordPress, Elementor, and hosting caches.

**Search or filters are not working**

Ensure the JavaScript file is correctly loaded and check for browser console errors.

**Download button is not working**

Make sure a valid, publicly accessible downloadable file has been assigned to the resource.

## Security & Limitations

- Resource management is restricted to authorized WordPress users.
- Uploaded file formats follow WordPress media upload restrictions.
- Downloadable resources are intended for public access.
- The plugin does not include membership, payment processing, or private document access controls.
- Avoid publishing confidential documents through the public resource library.

## Version History

### Version 2.0.0

- Improved responsive Resource Library design.
- Refined Elementor shortcode styling.
- Resource management through the WordPress Dashboard.
- Search and category filtering.
- Downloadable resource integration.
- Load More functionality.
- Sample resource for demonstration.

## Project Information

**Project Name:** Industry Atlas Resource Library

**Organization:** Industry Atlas Group

**Plugin Version:** 2.0.0

**Platform:** WordPress

**Integration:** Elementor / WordPress Shortcode

**Repository:** https://github.com/mahmudp275/industry-atlas-resource-library

---

**Industry Atlas Group**

*Connecting Manufacturing. Creating Opportunity.*

Helping manufacturers increase visibility, strengthen credibility, and build meaningful industry connections.
