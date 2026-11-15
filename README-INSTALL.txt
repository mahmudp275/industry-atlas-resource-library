INDUSTRY ATLAS — RESOURCE LIBRARY PLUGIN
=======================================

SHORTCODE:
    [iag_resource_library]

OPTIONAL CUSTOM TITLE:
    [iag_resource_library title="Downloads & Guides"]

OPTIONAL INITIAL CARD COUNT:
    [iag_resource_library limit="9"]

INSTALLATION:
1. WordPress Dashboard -> Plugins -> Add Plugin -> Upload Plugin.
2. Upload industry-atlas-resource-library-plugin.zip and activate.
3. Open the WordPress page in Elementor.
4. Drag in a SHORTCODE widget where the Resource Library section should appear.
5. Paste: [iag_resource_library]
6. Update and view the page.

CONTENT MANAGEMENT:
1. Dashboard -> Resource Library -> Add Resource.
2. Enter a Title and an Excerpt (short description).
3. Pick a Resource Category in the post editor sidebar.
4. In Downloadable File, upload/select an existing PDF or other file from Media Library;
   alternatively add a publicly reachable direct file URL.
5. Publish. The resource will appear automatically in the shortcode section.
6. Edit/Delete resources under Resource Library -> All Resources.
7. Manage topics under Resource Library -> Resource Categories.

SAMPLE RESOURCE:
On first activation, one clearly labeled sample resource is created and linked to a
real sample PDF file that ships with the plugin. It can be downloaded immediately.
Replace or remove this demo entry before public launch.

IMPORTANT:
- This plugin produces ONLY the Resource Library section.
- It does NOT create any new pages or modify the website navigation.
- Compatible with Elementor Shortcode widget and WordPress shortcode block.
- Admin can edit titles, descriptions, categories, downloadable files, and publication status.
- All search/category filtering and Load More controls work without page reload.
- This plugin uses a separate content type (iagrl_resource) to avoid interfering with
  the previous Industry Atlas News & Resources plugin.
- Keep one resource shortcode per page, or use multiple independent sections.
- Public file URLs are accessible without a login; do not upload confidential files.
- Upload types depend on your WordPress site's allowed media file types.
- Up to 200 published resources are included in the frontend query per section.
- For very large libraries, pagination/API enhancements would be appropriate.

FILE LIST:
industry-atlas-resource-library.php
assets/resource-library.css
assets/resource-library.js
assets/admin.js
assets/sample-manufacturing-visibility-checklist.pdf
README-INSTALL.txt
