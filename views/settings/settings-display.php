<?php defined('ABSPATH') || exit; ?>

<style>
  .display-tabs {
      display: flex;
    position: relative;
    gap: 10px;
    z-index: 10;
    justify-content: space-between;
  }

  .display-tab {
    border: 1px solid #ccc;
    padding: 5px 12px;
    border-radius: 4px 4px 0 0;
    cursor: pointer;
    background: #f4f4f4;
    font-weight: bold;
    color: #444;
    font-size: 14px;
    width: 100%;
  }

  .display-tab.active {
    background: #fff;
    border-bottom: 2px solid white;
    color: red;
  }

  .display-tab-contents {
     border: 1px solid #ccc;
    position: relative;
    padding: 0;
    top: -1px;
    z-index: 0;
  }

  .display-content {
    display: none;
    padding: 16px;
    background: #fff;
  }

  .display-content.active {
    display: block;
  }

  .form-grid {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 10px 20px;
    margin: 14px 0;
  }

  .form-inline {
    display: flex;
    gap: 40px;
    margin: 18px 0;
    align-items: center;
    justify-content: space-between;
  }

  .sub-slot-block {
    padding: 5px 0 25px;
  }
  .tab-green { color: green !important; border: 3px solid green; }
  .tab-red   { color: red !important; border: 3px solid red; }
  .tab-grey  { color: #595959ff !important; }
</style>

<script>
document.addEventListener('DOMContentLoaded', function () {
  const tabs = document.querySelectorAll('.display-tab');
  const contents = document.querySelectorAll('.display-content');
  tabs.forEach((tab, idx) => {
    tab.addEventListener('click', () => {
      tabs.forEach(t => t.classList.remove('active'));
      contents.forEach(c => c.classList.remove('active'));
      tab.classList.add('active');
      contents[idx].classList.add('active');
    });
  });
  if (tabs.length > 0) { tabs[0].click(); }
});
</script>

<div id="tab-display-slot" class="tab-content adx-tab hidden">
  <h2 class="tab-title">Display Slots</h2>
  <input type="hidden" name="display_slot_enabled" value="false" />
  <label style="margin-bottom:10px;display:block;">
    <input
      type="checkbox"
      id="display_slot_enabled"
      name="display_slot_enabled"
      value="true"
      <?php checked( get_option('display_slot_enabled'), 'true' ); ?> />
    Enable Display Ad
  </label>

  <!-- Tabs -->
  <div class="display-tabs flex w-full gap-2">
    <?php for ($i = 1; $i <= 10; $i++):
      $sub_enabled = get_option("display_slot_{$i}_enabled") === 'true';
      $code        = trim(get_option("display_slot_{$i}_network_code", ''));
      if ($code === '') {
        $tabClass = 'tab-grey';
      } elseif (!$sub_enabled) {
        $tabClass = 'tab-red';
      } else {
        $tabClass = 'tab-green';
      }
    ?>
      <div class="display-tab <?php echo esc_attr($tabClass); ?>">
        <?php echo esc_html($i); ?>
      </div>
    <?php endfor; ?>
  </div>

  <!-- Content -->
  <div class="display-tab-contents">
    <?php for ($i = 1; $i <= 10; $i++):
      $enabled   = get_option("display_slot_{$i}_enabled") === 'true';
      $code      = get_option("display_slot_{$i}_network_code", '');
      $pages     = get_option("display_slot_{$i}_pages", []);
      $insertion = get_option("display_slot_{$i}_insertion", '');
      $alignment = get_option("display_slot_{$i}_alignment", '');
      $text      = get_option("display_slot_{$i}_text", '');
      $offset    = get_option("display_slot_{$i}_offset", 0);
    ?>
      <div class="display-content">
        <div class="sub-slot-block">
          <h3 style="margin-top : 0;"><strong><?php printf('Block %s', esc_html($i)); ?></strong></h3>

          <!-- Enable Toggle -->
          <label style="margin-bottom:10px; display:block;">
            <input type="checkbox"
              name="display_slot_<?php echo esc_attr($i); ?>_enabled"
              value="true" <?php checked($enabled, true); ?>>
            <?php printf('Enable Block %s', esc_html($i)); ?>
          </label>

          <!-- Network Code -->
          <div>
            <label for="display_slot_<?php echo esc_attr($i); ?>_network_code"><strong>Network Code</strong></label>
            <input type="text"
              name="display_slot_<?php echo esc_attr($i); ?>_network_code"
              id="display_slot_<?php echo esc_attr($i); ?>_network_code"
              value="<?php echo esc_attr($code); ?>"
              style="width:100%; margin-top:5px;">
          </div>

          <!-- Page Types -->
          <div class="form-grid border-2 px-6 py-3 rounded-lg border-slate-200">
            <?php
              $types = [
                'post'     => 'Posts',
                'homepage' => 'Homepage',
                'category' => 'Category',
                'static'   => 'Static',
                'search'   => 'Search',
                'tag'      => 'Tag',
              ];
              foreach ($types as $val => $label) {
            ?>
              <label>
                <input type="checkbox"
                  name="display_slot_<?php echo esc_attr($i); ?>_pages[]"
                  value="<?php echo esc_attr($val); ?>"
                  <?php checked(in_array($val, (array)$pages), true); ?>>
                <?php echo esc_html($label); ?>
              </label>
            <?php } ?>
          </div>

          <!-- Insertion & Alignment -->
          <div class="form-inline">
            <div class="insertion">
              <label for="display_slot_<?php echo esc_attr($i); ?>_insertion"><strong>Insertion</strong></label>
              <select name="display_slot_<?php echo esc_attr($i); ?>_insertion" id="display_slot_<?php echo esc_attr($i); ?>_insertion">
                <?php
                  $insert_options = [
                    'before_post'      => 'Before Post',
                    'after_post'       => 'After Post',
                    'before_paragraph' => 'Before Paragraph',
                    'after_paragraph'  => 'After Paragraph',
                    'before_image'     => 'Before Image',
                    'after_image'      => 'After Image',
                  ];
                  foreach ($insert_options as $val => $label) {
                ?>
                  <option value="<?php echo esc_attr($val); ?>" <?php selected($insertion, $val); ?>>
                    <?php echo esc_html($label); ?>
                  </option>
                <?php } ?>
              </select>
            </div>

            <!-- Offset (Count) only for specific insertions -->
            <div class="offset">
              <?php $showOffset = in_array($insertion, ['before_paragraph','after_paragraph','before_image','after_image']); ?>
              <span class="offset-wrapper" style="<?php echo $showOffset ? '' : 'display:none;'; ?>">
                <label for="display_slot_<?php echo esc_attr($i); ?>_offset"><strong>Count</strong></label>
                <input type="number"
                  name="display_slot_<?php echo esc_attr($i); ?>_offset"
                  id="display_slot_<?php echo esc_attr($i); ?>_offset"
                  value="<?php echo esc_attr($offset); ?>"
                  min="1"
                  style="width:10em; margin-left:.5em;">
              </span>
            </div>

            <!-- Alignment -->
            <div class="alignment">
              <label for="display_slot_<?php echo esc_attr($i); ?>_alignment"><strong>Alignment</strong></label>
              <select name="display_slot_<?php echo esc_attr($i); ?>_alignment" id="display_slot_<?php echo esc_attr($i); ?>_alignment">
                <option value="start" <?php selected($alignment, 'start'); ?>>Start (Left)</option>
                <option value="center" <?php selected($alignment, 'center'); ?>>Center</option>
                <option value="end" <?php selected($alignment, 'end'); ?>>End (Right)</option>
              </select>
            </div>
          </div>

          <!-- Custom Message -->
          <div style="margin-top:12px;">
            <label for="display_slot_<?php echo esc_attr($i); ?>_text"><strong>Custom Message (TEMP)</strong></label>
            <input type="text"
              name="display_slot_<?php echo esc_attr($i); ?>_text"
              id="display_slot_<?php echo esc_attr($i); ?>_text"
              value="<?php echo esc_attr($text); ?>"
              style="width:100%;margin-top:5px;"
              placeholder="Enter text to show on page">
          </div>
        </div>
      </div>
    <?php endfor; ?>
  </div>
  <script>
    document.addEventListener('DOMContentLoaded', () => {
      document.querySelectorAll('[id^="display_slot_"][id$="_insertion"]').forEach(select => {
        select.addEventListener('change', e => {
          const idx = e.target.id.match(/\d+/)[0];
          const wrapper = document.getElementById(`display_slot_${idx}_offset`).closest('.offset-wrapper');
          if (['before_paragraph','after_paragraph','before_image','after_image'].includes(e.target.value)) {
            wrapper.style.display = '';
          } else {
            wrapper.style.display = 'none';
          }
        });
      });
    });
  </script>
</div>
