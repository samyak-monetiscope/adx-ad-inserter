<?php
// File: /wp-content/plugins/adx-ad-inserter/views/settings-flying-carpet.php
// Usage: Replace your current <div id="tab-flying">…</div> block with this entire code.

defined('ABSPATH') || exit;

// Read saved options (with safe defaults)
$flying_enabled     = get_option('flying_enabled');
$flying_network     = get_option('flying_network_code', '');
$flying_insertion   = get_option('flying_insertion', '');
$flying_offset      = (int) get_option('flying_offset', 1);
$flying_pages       = (array) get_option('flying_pages', []);
$flying_devices_sel = (array) get_option('flying_devices', ['desktop','mobile']);
$flying_alignment   = get_option('flying_alignment', 'center'); // NEW

// Refs for UI
$page_types = [
  'post'     => 'Posts',
  'homepage' => 'Homepage',
  'category' => 'Category',
  'static'   => 'Static',
  'search'   => 'Search',
  'tag'      => 'Tag',
];
$device_types = [
  'desktop' => 'Desktop',
  'mobile'  => 'Mobile/Tablet',
];
$align_types = [
  'left'   => 'Left',
  'center' => 'Center',
  'right'  => 'Right',
];

// Show offset field only for these insertions
$needs_offset = in_array($flying_insertion, ['before_paragraph','after_paragraph','before_image','after_image'], true);
?>
<div id="tab-flying" class="tab-content adx-tab hidden">
  <h2 class="tab-title">Flying Carpet Ad</h2>

  <!-- Enable -->
  <input type="hidden" name="flying_enabled" value="false" />
  <label style="margin-bottom:10px;display:block;">
    <input
      type="checkbox"
      id="flying_enabled"
      name="flying_enabled"
      value="true"
      <?php checked($flying_enabled, 'true'); ?> />
    Enable Flying Carpet Ad
  </label>

  <!-- Network Code -->
  <div style="margin: 10px 0 18px;">
    <label for="flying_network_code"><strong>Flying Carpet Network Code</strong></label><br>
    <input
      type="text"
      id="flying_network_code"
      name="flying_network_code"
      value="<?php echo esc_attr($flying_network); ?>"
      class="regular-text"
      style="width:100%;max-width:700px;margin-top:6px;" />
  </div>

  <div class="flex justify-between w-full gap-10">
    <!-- Insertion -->
    <div style="width:-webkit-fill-available;">
      <label for="flying_insertion"><strong>Insertion</strong></label>
      <select
        id="flying_insertion"
        name="flying_insertion"
        style="width:100%;margin-top:6px;">
        <option value="before_post"      <?php selected($flying_insertion, 'before_post'); ?>>Before Post</option>
        <option value="after_post"       <?php selected($flying_insertion, 'after_post'); ?>>After Post</option>
        <option value="before_paragraph" <?php selected($flying_insertion, 'before_paragraph'); ?>>Before Paragraph</option>
        <option value="after_paragraph"  <?php selected($flying_insertion, 'after_paragraph'); ?>>After Paragraph</option>
        <option value="before_image"     <?php selected($flying_insertion, 'before_image'); ?>>Before Image</option>
        <option value="after_image"      <?php selected($flying_insertion, 'after_image'); ?>>After Image</option>
      </select>
    </div>

    <!-- Offset (conditional) -->
    <div id="flying-offset-wrapper" class="offset-wrapper" style="<?php echo $needs_offset ? '' : 'display:none;'; ?>">
      <label for="flying_offset"><strong>Number</strong></label>
      <input
        type="number"
        id="flying_offset"
        name="flying_offset"
        value="<?php echo esc_attr(max(1, $flying_offset)); ?>"
        min="1"
        max="50"
        style="width:90px;margin-top:6px;">
    </div>

    <!-- Alignment (NEW) -->
    <div style="width:-webkit-fill-available;">
      <label for="flying_alignment"><strong>Alignment</strong></label>
      <select
        id="flying_alignment"
        name="flying_alignment"
        style="width:100%;margin-top:6px;">
        <option value="left"   <?php selected($flying_alignment, 'left'); ?>>Left</option>
        <option value="center" <?php selected($flying_alignment, 'center'); ?>>Center</option>
        <option value="right"  <?php selected($flying_alignment, 'right'); ?>>Right</option>
      </select>
    </div>
  </div>

  <!-- Page Types -->
  <div class="form-grid border-2 px-6 py-3 rounded-lg border-slate-200" style="margin-top:14px;">
    <?php foreach ($page_types as $val => $label): ?>
      <label>
        <input
          type="checkbox"
          name="flying_pages[]"
          value="<?php echo esc_attr($val); ?>"
          <?php checked(in_array($val, $flying_pages, true), true); ?> />
        <?php echo esc_html($label); ?>
      </label>
    <?php endforeach; ?>
  </div>

  <!-- Devices -->
  <div class="form-grid border-2 px-6 py-3 rounded-lg border-slate-200" style="margin-top:14px;">
    <?php foreach ($device_types as $key => $label): ?>
      <label style="margin-right:1em;">
        <input
          type="checkbox"
          name="flying_devices[]"
          value="<?php echo esc_attr($key); ?>"
          <?php checked(in_array($key, $flying_devices_sel, true), true); ?> />
        <?php echo esc_html($label); ?>
      </label>
    <?php endforeach; ?>
  </div>

  <script>
    document.addEventListener('DOMContentLoaded', function () {
      const insertion = document.getElementById('flying_insertion');
      const offsetWrap = document.getElementById('flying-offset-wrapper');

      function toggleOffset() {
        if (['before_paragraph','after_paragraph','before_image','after_image'].includes(insertion.value)) {
          offsetWrap.style.display = '';
        } else {
          offsetWrap.style.display = 'none';
        }
      }

      insertion.addEventListener('change', toggleOffset);
      toggleOffset(); // initial state
    });
  </script>
</div>
