<?php
defined('ABSPATH') || exit;
?>
<div id="tab-flying" class="adx-tab" style="display:none">
    <h3>Flying Carpet Ad</h3>

    <p>
        <input type="hidden" name="flying_enabled" value="false" />
        <label>
            <input type="checkbox"
                   id="flying_enabled"
                   name="flying_enabled"
                   value="true" <?php checked(get_option('flying_enabled'), 'true'); ?> />
            Enable Flying Carpet Ad
        </label>
    </p>

    <p>
        <label for="flying_network_code"><strong>Flying Carpet Network Code</strong></label><br>
        <input type="text"
               id="flying_network_code"
               name="flying_network_code"
               value="<?php echo esc_attr(get_option('flying_network_code')); ?>"
               class="regular-text" />
    </p>

    <p>
        <label for="flying_insertion"><strong>Insertion Position</strong></label><br>
        <select id="flying_insertion" name="flying_insertion">
            <?php $insertion = get_option('flying_insertion', 'after_post'); ?>
            <option value="before_post" <?php selected($insertion, 'before_post'); ?>>Before Post (before first H1)</option>
            <option value="after_post" <?php selected($insertion, 'after_post'); ?>>After Post</option>
            <option value="before_paragraph" <?php selected($insertion, 'before_paragraph'); ?>>Before Paragraph (Nth)</option>
            <option value="after_paragraph" <?php selected($insertion, 'after_paragraph'); ?>>After Paragraph (Nth)</option>
            <option value="before_image" <?php selected($insertion, 'before_image'); ?>>Before Image (Nth)</option>
            <option value="after_image" <?php selected($insertion, 'after_image'); ?>>After Image (Nth)</option>
        </select>
    </p>

    <p>
        <label for="flying_offset"><strong>Offset (Nth paragraph or image)</strong></label><br>
        <input type="number"
               id="flying_offset"
               name="flying_offset"
               value="<?php echo esc_attr(get_option('flying_offset', 0)); ?>"
               min="0"
               step="1" />
        <em>Leave 0 to append at end.</em>
    </p>

    <p><strong>Page Types</strong><br>
        <?php $pages = (array) get_option('flying_pages', []); ?>
        <label><input type="checkbox" name="flying_pages[]" value="post"     <?php checked(in_array('post', $pages)); ?>> Posts</label><br>
        <label><input type="checkbox" name="flying_pages[]" value="static"   <?php checked(in_array('static', $pages)); ?>> Static Pages</label><br>
        <label><input type="checkbox" name="flying_pages[]" value="homepage" <?php checked(in_array('homepage', $pages)); ?>> Homepage / Blog</label><br>
        <label><input type="checkbox" name="flying_pages[]" value="search"   <?php checked(in_array('search', $pages)); ?>> Search Results</label><br>
        <label><input type="checkbox" name="flying_pages[]" value="category" <?php checked(in_array('category', $pages)); ?>> Category Archives</label><br>
        <label><input type="checkbox" name="flying_pages[]" value="tag"      <?php checked(in_array('tag', $pages)); ?>> Tag Archives</label>
    </p>

    <p><strong>Devices</strong><br>
        <?php $devices = (array) get_option('flying_devices', []); ?>
        <label><input type="checkbox" name="flying_devices[]" value="desktop" <?php checked(in_array('desktop', $devices)); ?>> Desktop</label><br>
        <label><input type="checkbox" name="flying_devices[]" value="mobile"  <?php checked(in_array('mobile', $devices)); ?>> Mobile</label><br>
        <label><input type="checkbox" name="flying_devices[]" value="tablet"  <?php checked(in_array('tablet', $devices)); ?>> Tablet</label>
    </p>
</div>
