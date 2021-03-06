<?php

if (!defined('ABSPATH')) exit;

?>
<div class="wrap" id="af-options">
    <?php
    $title = __('Settings', 'asgaros-forum');
    $titleUpdated = __('Settings updated.', 'asgaros-forum');
    $this->render_admin_header($title, $titleUpdated);
    ?>

    <form method="post">
        <?php wp_nonce_field('asgaros_forum_save_options'); ?>

        <?php

        // Build array of available tabs.
        $tabs = array(
            'general' => array(
                'label' => __('General', 'asgaros-forum'),
                'icon' => 'fas fa-sliders-h'
            ),
            'features' => array(
                'label' => __('Features', 'asgaros-forum'),
                'icon' => 'fas fa-plug'
            ),
            'urls' => array(
                'label' => __('URLs', 'asgaros-forum'),
                'icon' => 'fas fa-link'
            ),
            'permissions' => array(
                'label' => __('Permissions', 'asgaros-forum'),
                'icon' => 'fas fa-user-shield'
            ),
            'breadcrumbs' => array(
                'label' => __('Breadcrumbs', 'asgaros-forum'),
                'icon' => 'fas fa-map-marked'
            ),
            'notifications' => array(
                'label' => __('Notifications', 'asgaros-forum'),
                'icon' => 'fas fa-envelope'
            ),
            'mentioning' => array(
                'label' => __('Mentioning', 'asgaros-forum'),
                'icon' => 'fas fa-at'
            ),
            'memberslist' => array(
                'label' => __('Members List', 'asgaros-forum'),
                'icon' => 'fas fa-users'
            ),
            'profiles' => array(
                'label' => __('Profiles', 'asgaros-forum'),
                'icon' => 'fas fa-user'
            ),
            'uploads' => array(
                'label' => __('Uploads', 'asgaros-forum'),
                'icon' => 'fas fa-upload'
            ),
            'reports' => array(
                'label' => __('Reports', 'asgaros-forum'),
                'icon' => 'fas fa-exclamation-triangle'
            ),
            'signatures' => array(
                'label' => __('Signatures', 'asgaros-forum'),
                'icon' => 'fas fa-signature'
            ),
            'activity' => array(
                'label' => __('Activity', 'asgaros-forum'),
                'icon' => 'fas fa-bullhorn'
            ),
            'ads' => array(
                'label' => __('Ads', 'asgaros-forum'),
                'icon' => 'fas fa-ad'
            ),
            'polls' => array(
                'label' => __('Polls', 'asgaros-forum'),
                'icon' => 'fas fa-poll-h'
            ),
            'spoilers' => array(
                'label' => __('Spoilers', 'asgaros-forum'),
                'icon' => 'fas fa-eye-slash'
            )
        );

        // Get selected tab.
        $selected_tab = 'general';

        if (isset($_POST['selected_tab']) && isset($tabs[$_POST['selected_tab']])) {
            $selected_tab = $_POST['selected_tab'];
        }

        // Generate hidden input for selected tab.
        echo '<input type="hidden" name="selected_tab" value="'.$selected_tab.'">';

        ?>

        <div id="settings-wrapper">
            <ul id="settings-tabs">
                <?php
                foreach ($tabs as $key => $value) {
                    $active_css = ($selected_tab == $key) ? 'class="active-tab"' : '';

                    echo '<li data-slug="'.$key.'" '.$active_css.'>';
                    echo '<a href="#asgaros-panel">';
                    echo '<i class="'.$value['icon'].'"></i>';
                    echo '<span>'.$value['label'].'</span>';
                    echo '</a>';
                    echo '</li>';
                }
                ?>
            </ul>

            <div id="tab-content">
                <?php $display = ($selected_tab == 'general') ? 'block' : 'none'; ?>
                <div class="tab" id="tab-general" style="display: <?php echo $display; ?>;">
                    <div class="settings-header">
                        <span class="fas fa-sliders-h"></span>
                        <?php _e('General', 'asgaros-forum'); ?>
                    </div>
                    <table>
                        <tr>
                            <th><label for="forum_title"><?php _e('Forum title:', 'asgaros-forum'); ?></label></th>
                            <td><input class="regular-text" type="text" name="forum_title" id="forum_title" value="<?php echo esc_html(stripslashes($asgarosforum->options['forum_title'])); ?>"></td>
                        </tr>

                        <tr>
                            <th><label for="location"><?php _e('Forum location:', 'asgaros-forum'); ?></label></th>
                            <td>
                                <?php
                                // Set a post_status argument because of a core bug. See: https://core.trac.wordpress.org/ticket/8592
                                wp_dropdown_pages(array('selected' => esc_attr($asgarosforum->options['location']), 'name' => 'location', 'id' => 'location', 'post_status' => array('publish', 'pending', 'draft', 'private')));
                                echo '<span class="description">'.__('Page which contains the [forum]-shortcode.', 'asgaros-forum').'</span>';
                                ?>
                            </td>
                        </tr>

                        <tr>
                            <th><label for="posts_per_page"><?php _e('Replies to show per page:', 'asgaros-forum'); ?></label></th>
                            <td><input type="number" name="posts_per_page" id="posts_per_page" value="<?php echo stripslashes($asgarosforum->options['posts_per_page']); ?>" size="3" min="1"></td>
                        </tr>

                        <tr>
                            <th><label for="topics_per_page"><?php _e('Topics to show per page:', 'asgaros-forum'); ?></label></th>
                            <td><input type="number" name="topics_per_page" id="topics_per_page" value="<?php echo stripslashes($asgarosforum->options['topics_per_page']); ?>" size="3" min="1"></td>
                        </tr>

                        <tr>
                            <th><label for="create_blog_topics"><?php _e('Create topics for new blog posts in the following forum:', 'asgaros-forum'); ?></label></th>
                            <td>
                                <?php
                                echo '<select name="create_blog_topics_id">';

                                echo '<option value="0"'.(0 == $asgarosforum->options['create_blog_topics_id'] ? ' selected="selected"' : '').'>'.__('Dont create topics', 'asgaros-forum').'</option>';

                                $categories = $asgarosforum->content->get_categories(false);

                                if ($categories) {
                                    foreach ($categories as $category) {
                                        $forums = $asgarosforum->get_forums($category->term_id, 0);

                                        if ($forums) {
                                            foreach ($forums as $forum) {
                                                echo '<option value="'.$forum->id.'"'.($forum->id == $asgarosforum->options['create_blog_topics_id'] ? ' selected="selected"' : '').'>'.esc_html($forum->name).'</option>';

                                                if ($forum->count_subforums > 0) {
                                                    $subforums = $asgarosforum->get_forums($category->term_id, $forum->id);

                                                    foreach ($subforums as $subforum) {
                                                        echo '<option value="'.$subforum->id.'"'.($subforum->id == $asgarosforum->options['create_blog_topics_id'] ? ' selected="selected"' : '').'>--- '.esc_html($subforum->name).'</option>';
                                                    }
                                                }
                                            }
                                        }
                                    }
                                }

                                echo '</select>';
                                ?>
                            </td>
                        </tr>

                        <tr>
                            <th><label for="minimalistic_editor"><?php _e('Use minimalistic editor', 'asgaros-forum'); ?></label></th>
                            <td><input type="checkbox" name="minimalistic_editor" id="minimalistic_editor" <?php checked(!empty($asgarosforum->options['minimalistic_editor'])); ?>></td>
                        </tr>

                        <tr>
                            <th><label for="allow_shortcodes"><?php _e('Allow shortcodes in posts', 'asgaros-forum'); ?></label></th>
                            <td><input type="checkbox" name="allow_shortcodes" id="allow_shortcodes" <?php checked(!empty($asgarosforum->options['allow_shortcodes'])); ?>></td>
                        </tr>

                        <tr>
                            <th><label for="embed_content"><?php _e('Automatically embed content in posts', 'asgaros-forum'); ?></label></th>
                            <td><input type="checkbox" name="embed_content" id="embed_content" <?php checked(!empty($asgarosforum->options['embed_content'])); ?>></td>
                        </tr>

                        <tr>
                            <th><label for="highlight_admin"><?php _e('Highlight administrator/moderator names', 'asgaros-forum'); ?></label></th>
                            <td><input type="checkbox" name="highlight_admin" id="highlight_admin" <?php checked(!empty($asgarosforum->options['highlight_admin'])); ?>></td>
                        </tr>

                        <tr>
                            <th><label for="highlight_authors"><?php _e('Highlight topic authors', 'asgaros-forum'); ?></label></th>
                            <td><input type="checkbox" name="highlight_authors" id="highlight_authors" <?php checked(!empty($asgarosforum->options['highlight_authors'])); ?>></td>
                        </tr>

                        <tr>
                            <th><label for="show_author_posts_counter"><?php _e('Show author posts counter', 'asgaros-forum'); ?></label></th>
                            <td><input type="checkbox" name="show_author_posts_counter" id="show_author_posts_counter" <?php checked(!empty($asgarosforum->options['show_author_posts_counter'])); ?>></td>
                        </tr>

                        <tr>
                            <th><label for="show_description_in_forum"><?php _e('Show description in forum', 'asgaros-forum'); ?></label></th>
                            <td><input type="checkbox" name="show_description_in_forum" id="show_description_in_forum" <?php checked(!empty($asgarosforum->options['show_description_in_forum'])); ?>></td>
                        </tr>

                        <tr>
                            <th><label for="require_login"><?php _e('Hide forum from logged-out users', 'asgaros-forum'); ?></label></th>
                            <td><input type="checkbox" name="require_login" id="require_login" <?php checked(!empty($asgarosforum->options['require_login'])); ?>></td>
                        </tr>

                        <tr>
                            <th>
                                <label for="require_login_posts"><?php _e('Hide posts from logged-out users', 'asgaros-forum'); ?></label>
                                <span class="description"><?php _e('Guests can see topics but need to log in to access the posts they contain.', 'asgaros-forum'); ?></span>
                            </th>
                            <td>
                                <input type="checkbox" name="require_login_posts" id="require_login_posts" <?php checked(!empty($asgarosforum->options['require_login_posts'])); ?>>
                            </td>
                        </tr>

                        <tr>
                            <th><label for="show_login_button"><?php _e('Show login button', 'asgaros-forum'); ?></label></th>
                            <td><input type="checkbox" name="show_login_button" id="show_login_button" <?php checked(!empty($asgarosforum->options['show_login_button'])); ?>></td>
                        </tr>

                        <tr>
                            <th><label for="show_logout_button"><?php _e('Show logout button', 'asgaros-forum'); ?></label></th>
                            <td><input type="checkbox" name="show_logout_button" id="show_logout_button" <?php checked(!empty($asgarosforum->options['show_logout_button'])); ?>></td>
                        </tr>

                        <tr>
                            <th><label for="show_register_button"><?php _e('Show register button', 'asgaros-forum'); ?></label></th>
                            <td><input type="checkbox" name="show_register_button" id="show_register_button" <?php checked(!empty($asgarosforum->options['show_register_button'])); ?>></td>
                        </tr>

                        <tr>
                            <th><label for="show_edit_date"><?php _e('Show edit date', 'asgaros-forum'); ?></label></th>
                            <td><input type="checkbox" name="show_edit_date" id="show_edit_date" <?php checked(!empty($asgarosforum->options['show_edit_date'])); ?>></td>
                        </tr>

                        <tr>
                            <th>
                                <label for="approval_for"><?php _e('Approval needed for new topics from:', 'asgaros-forum'); ?></label>
                                <span class="description"><?php _e('This setting only affects forums that require approval for new topics.', 'asgaros-forum'); ?></span>
                            </th>
                            <td>
                                <select name="approval_for" id="approval_for">';
                                    <option value="guests" <?php if ($asgarosforum->options['approval_for'] == 'guests') { echo 'selected="selected"'; } ?>><?php _e('Guests', 'asgaros-forum'); ?></option>
                                    <option value="normal" <?php if ($asgarosforum->options['approval_for'] == 'normal') { echo 'selected="selected"'; } ?>><?php _e('Guests & Normal Users', 'asgaros-forum'); ?></option>
                                </select>
                            </td>
                        </tr>

                        <tr>
                            <th>
                                <label for="subforums_location"><?php _e('Location of subforums:', 'asgaros-forum'); ?></label>
                            </th>
                            <td>
                                <select name="subforums_location" id="subforums_location">';
                                    <option value="above" <?php if ($asgarosforum->options['subforums_location'] == 'above') { echo 'selected="selected"'; } ?>><?php _e('Above Topics', 'asgaros-forum'); ?></option>
                                    <option value="below" <?php if ($asgarosforum->options['subforums_location'] == 'below') { echo 'selected="selected"'; } ?>><?php _e('Below Topics', 'asgaros-forum'); ?></option>
                                    <option value="both" <?php if ($asgarosforum->options['subforums_location'] == 'both') { echo 'selected="selected"'; } ?>><?php _e('Above & Below Topics', 'asgaros-forum'); ?></option>
                                </select>
                            </td>
                        </tr>
                    </table>
                </div>

                <?php $display = ($selected_tab == 'features') ? 'block' : 'none'; ?>
                <div class="tab" id="tab-features" style="display: <?php echo $display; ?>;">
                    <div class="settings-header">
                        <span class="fas fa-plug"></span>
                        <?php _e('Features', 'asgaros-forum'); ?>
                    </div>
                    <table>
                        <tr>
                            <th><label for="enable_avatars"><?php _e('Enable Avatars', 'asgaros-forum'); ?></label></th>
                            <td><input type="checkbox" name="enable_avatars" id="enable_avatars" <?php checked(!empty($asgarosforum->options['enable_avatars'])); ?>></td>
                        </tr>
                        <tr>
                            <th><label for="enable_reactions"><?php _e('Enable reactions', 'asgaros-forum'); ?></label></th>
                            <td><input type="checkbox" name="enable_reactions" id="enable_reactions" <?php checked(!empty($asgarosforum->options['enable_reactions'])); ?>></td>
                        </tr>
                        <tr>
                            <th><label for="enable_search"><?php _e('Enable search functionality', 'asgaros-forum'); ?></label></th>
                            <td><input type="checkbox" name="enable_search" id="enable_search" <?php checked(!empty($asgarosforum->options['enable_search'])); ?>></td>
                        </tr>
                        <tr>
                            <th><label for="enable_rss"><?php _e('Enable RSS Feeds', 'asgaros-forum'); ?></label></th>
                            <td><input type="checkbox" name="enable_rss" id="enable_rss" <?php checked(!empty($asgarosforum->options['enable_rss'])); ?>></td>
                        </tr>
                        <tr>
                            <th><label for="count_topic_views"><?php _e('Count topic views', 'asgaros-forum'); ?></label></th>
                            <td><input type="checkbox" name="count_topic_views" id="count_topic_views" <?php checked(!empty($asgarosforum->options['count_topic_views'])); ?>></td>
                        </tr>
                        <tr>
                            <th><label for="show_who_is_online"><?php _e('Show who is online', 'asgaros-forum'); ?></label></th>
                            <td><input type="checkbox" name="show_who_is_online" id="show_who_is_online" <?php checked(!empty($asgarosforum->options['show_who_is_online'])); ?>></td>
                        </tr>
                        <tr>
                            <th><label for="show_statistics"><?php _e('Show statistics', 'asgaros-forum'); ?></label></th>
                            <td><input type="checkbox" name="show_statistics" id="show_statistics" <?php checked(!empty($asgarosforum->options['show_statistics'])); ?>></td>
                        </tr>
                        <tr>
                            <th><label for="allow_guest_postings"><?php _e('Allow guest postings', 'asgaros-forum'); ?></label></th>
                            <td><input type="checkbox" name="allow_guest_postings" id="allow_guest_postings" <?php checked(!empty($asgarosforum->options['allow_guest_postings'])); ?>></td>
                        </tr>
                        <tr>
                            <th>
                                <label for="load_fontawesome"><?php _e('Load Font Awesome v5 icon library', 'asgaros-forum'); ?></label>
                                <span class="description"><?php _e('You can disable loading the built-in Font Awesome v5 icon library to reduce traffic if your theme or another plugin already loads this library.', 'asgaros-forum'); ?></span>
                            </th>
                            <td><input type="checkbox" name="load_fontawesome" id="load_fontawesome" <?php checked(!empty($asgarosforum->options['load_fontawesome'])); ?>></td>
                        </tr>
                        <tr>
                            <th>
                                <label for="load_fontawesome_compat_v4"><?php _e('Load Font Awesome v4 compatibility library', 'asgaros-forum'); ?></label>
                                <span class="description"><?php _e('The Font Awesome v4 compatibility library is required, if your theme or another plugin uses the Font Awesome v4 icon library. If the Font Awesome v4 icon library is not used on your website, you can disable this option.', 'asgaros-forum'); ?></span>
                            </th>
                            <td><input type="checkbox" name="load_fontawesome_compat_v4" id="load_fontawesome_compat_v4" <?php checked(!empty($asgarosforum->options['load_fontawesome_compat_v4'])); ?>></td>
                        </tr>
                    </table>
                </div>

                <?php $display = ($selected_tab == 'urls') ? 'block' : 'none'; ?>
                <div class="tab" id="tab-urls" style="display: <?php echo $display; ?>;">
                    <div class="settings-header">
                        <span class="fas fa-link"></span>
                        <?php _e('URLs', 'asgaros-forum'); ?>
                    </div>
                    <?php
                    $seo_option = checked(!empty($asgarosforum->options['enable_seo_urls']), true, false);
                    ?>
                    <table>
                        <tr>
                            <th><label for="enable_seo_urls"><?php _e('Enable SEO-friendly URLs', 'asgaros-forum'); ?></label></th>
                            <td><input type="checkbox" name="enable_seo_urls" id="enable_seo_urls" class="show_hide_initiator" data-hide-class="seo-option" <?php checked(!empty($asgarosforum->options['enable_seo_urls'])); ?>></td>
                        </tr>
                        <tr class="seo-option" <?php if (!$seo_option) { echo 'style="display: none;"'; } ?>>
                            <th>
                                <label for="seo_url_mode_content"><?php _e('URL mode for forums & topics:', 'asgaros-forum'); ?></label>
                                <span class="description"><?php _e('Define if the slug or the ID should be used in URLs for forums and topics. This setting is useful if you encounter problems when your slugs include special characters.', 'asgaros-forum'); ?></span>
                            </th>
                            <td>
                                <select name="seo_url_mode_content" id="seo_url_mode_content">';
                                    <option value="slug" <?php if ($asgarosforum->options['seo_url_mode_content'] == 'slug') { echo 'selected="selected"'; } ?>><?php _e('Slug', 'asgaros-forum'); ?></option>
                                    <option value="id" <?php if ($asgarosforum->options['seo_url_mode_content'] == 'id') { echo 'selected="selected"'; } ?>><?php _e('ID', 'asgaros-forum'); ?></option>
                                </select>
                            </td>
                        </tr>
                        <tr class="seo-option" <?php if (!$seo_option) { echo 'style="display: none;"'; } ?>>
                            <th>
                                <label for="seo_url_mode_profile"><?php _e('URL mode for profiles:', 'asgaros-forum'); ?></label>
                                <span class="description"><?php _e('Define if the slug or the ID should be used in URLs for profiles. This setting is useful if you want to hide the unique nicename of users from the public.', 'asgaros-forum'); ?></span>
                            </th>
                            <td>
                                <select name="seo_url_mode_profile" id="seo_url_mode_profile">';
                                    <option value="slug" <?php if ($asgarosforum->options['seo_url_mode_profile'] == 'slug') { echo 'selected="selected"'; } ?>><?php _e('Slug', 'asgaros-forum'); ?></option>
                                    <option value="id" <?php if ($asgarosforum->options['seo_url_mode_profile'] == 'id') { echo 'selected="selected"'; } ?>><?php _e('ID', 'asgaros-forum'); ?></option>
                                </select>
                            </td>
                        </tr>
                        <tr>
                            <th>
                                <label><?php _e('URL-slugs for views:', 'asgaros-forum'); ?></label>
                            </th>
                            <td>
                                <table>
                                    <tr>
                                        <th><label for="view_name_activity">activity:</label></th>
                                        <td><input class="regular-text" type="text" name="view_name_activity" id="view_name_activity" value="<?php echo esc_html(stripslashes($asgarosforum->options['view_name_activity'])); ?>"></td>
                                    </tr>
                                    <tr>
                                        <th><label for="view_name_subscriptions">subscriptions:</label></th>
                                        <td><input class="regular-text" type="text" name="view_name_subscriptions" id="view_name_subscriptions" value="<?php echo esc_html(stripslashes($asgarosforum->options['view_name_subscriptions'])); ?>"></td>
                                    </tr>
                                    <tr>
                                        <th><label for="view_name_search">search:</label></th>
                                        <td><input class="regular-text" type="text" name="view_name_search" id="view_name_search" value="<?php echo esc_html(stripslashes($asgarosforum->options['view_name_search'])); ?>"></td>
                                    </tr>
                                    <tr>
                                        <th><label for="view_name_forum">forum:</label></th>
                                        <td><input class="regular-text" type="text" name="view_name_forum" id="view_name_forum" value="<?php echo esc_html(stripslashes($asgarosforum->options['view_name_forum'])); ?>"></td>
                                    </tr>
                                    <tr>
                                        <th><label for="view_name_topic">topic:</label></th>
                                        <td><input class="regular-text" type="text" name="view_name_topic" id="view_name_topic" value="<?php echo esc_html(stripslashes($asgarosforum->options['view_name_topic'])); ?>"></td>
                                    </tr>
                                    <tr>
                                        <th><label for="view_name_addtopic">addtopic:</label></th>
                                        <td><input class="regular-text" type="text" name="view_name_addtopic" id="view_name_addtopic" value="<?php echo esc_html(stripslashes($asgarosforum->options['view_name_addtopic'])); ?>"></td>
                                    </tr>
                                    <tr>
                                        <th><label for="view_name_movetopic">movetopic:</label></th>
                                        <td><input class="regular-text" type="text" name="view_name_movetopic" id="view_name_movetopic" value="<?php echo esc_html(stripslashes($asgarosforum->options['view_name_movetopic'])); ?>"></td>
                                    </tr>
                                    <tr>
                                        <th><label for="view_name_addpost">addpost:</label></th>
                                        <td><input class="regular-text" type="text" name="view_name_addpost" id="view_name_addpost" value="<?php echo esc_html(stripslashes($asgarosforum->options['view_name_addpost'])); ?>"></td>
                                    </tr>
                                    <tr>
                                        <th><label for="view_name_editpost">editpost:</label></th>
                                        <td><input class="regular-text" type="text" name="view_name_editpost" id="view_name_editpost" value="<?php echo esc_html(stripslashes($asgarosforum->options['view_name_editpost'])); ?>"></td>
                                    </tr>
                                    <tr>
                                        <th><label for="view_name_markallread">markallread:</label></th>
                                        <td><input class="regular-text" type="text" name="view_name_markallread" id="view_name_markallread" value="<?php echo esc_html(stripslashes($asgarosforum->options['view_name_markallread'])); ?>"></td>
                                    </tr>
                                    <tr>
                                        <th><label for="view_name_members">members:</label></th>
                                        <td><input class="regular-text" type="text" name="view_name_members" id="view_name_members" value="<?php echo esc_html(stripslashes($asgarosforum->options['view_name_members'])); ?>"></td>
                                    </tr>
                                    <tr>
                                        <th><label for="view_name_profile">profile:</label></th>
                                        <td><input class="regular-text" type="text" name="view_name_profile" id="view_name_profile" value="<?php echo esc_html(stripslashes($asgarosforum->options['view_name_profile'])); ?>"></td>
                                    </tr>
                                    <tr>
                                        <th><label for="view_name_history">history:</label></th>
                                        <td><input class="regular-text" type="text" name="view_name_history" id="view_name_history" value="<?php echo esc_html(stripslashes($asgarosforum->options['view_name_history'])); ?>"></td>
                                    </tr>
                                    <tr>
                                        <th><label for="view_name_unread">unread:</label></th>
                                        <td><input class="regular-text" type="text" name="view_name_unread" id="view_name_unread" value="<?php echo esc_html(stripslashes($asgarosforum->options['view_name_unread'])); ?>"></td>
                                    </tr>
                                    <tr>
                                        <th><label for="view_name_unapproved">unapproved:</label></th>
                                        <td><input class="regular-text" type="text" name="view_name_unapproved" id="view_name_unapproved" value="<?php echo esc_html(stripslashes($asgarosforum->options['view_name_unapproved'])); ?>"></td>
                                    </tr>
                                    <tr>
                                        <th><label for="view_name_reports">reports:</label></th>
                                        <td><input class="regular-text" type="text" name="view_name_reports" id="view_name_reports" value="<?php echo esc_html(stripslashes($asgarosforum->options['view_name_reports'])); ?>"></td>
                                    </tr>
                                </table>
                            </td>
                        </tr>
                    </table>
                </div>

                <?php $display = ($selected_tab == 'permissions') ? 'block' : 'none'; ?>
                <div class="tab" id="tab-permissions" style="display: <?php echo $display; ?>;">
                    <div class="settings-header">
                        <span class="fas fa-user-shield"></span>
                        <?php _e('Permissions', 'asgaros-forum'); ?>
                    </div>
                    <table>
                        <tr>
                            <th><label for="enable_edit_post"><?php _e('Users can edit their own posts', 'asgaros-forum'); ?></label></th>
                            <td><input type="checkbox" name="enable_edit_post" id="enable_edit_post" class="show_hide_initiator" data-hide-class="edit-post-option" <?php checked(!empty($asgarosforum->options['enable_edit_post'])); ?>></td>
                        </tr>

                        <?php
                        $edit_post_option = checked(!empty($asgarosforum->options['enable_edit_post']), true, false);
                        ?>

                        <tr class="edit-post-option" <?php if (!$edit_post_option) { echo 'style="display: none;"'; } ?>>
                            <th><label for="time_limit_edit_posts"><?php _e('Time limitation for editing posts (in minutes):', 'asgaros-forum'); ?></label></th>
                            <td>
                                <input type="number" name="time_limit_edit_posts" id="time_limit_edit_posts" value="<?php echo stripslashes($asgarosforum->options['time_limit_edit_posts']); ?>" size="3" min="0">
                                <span class="description"><?php _e('(0 = No limitation)', 'asgaros-forum'); ?></span>
                            </td>
                        </tr>

                        <tr>
                            <th><label for="enable_delete_post"><?php _e('Users can delete their own posts', 'asgaros-forum'); ?></label></th>
                            <td><input type="checkbox" name="enable_delete_post" id="enable_delete_post" class="show_hide_initiator" data-hide-class="delete-post-option" <?php checked(!empty($asgarosforum->options['enable_delete_post'])); ?>></td>
                        </tr>

                        <?php
                        $delete_post_option = checked(!empty($asgarosforum->options['enable_delete_post']), true, false);
                        ?>

                        <tr class="delete-post-option" <?php if (!$delete_post_option) { echo 'style="display: none;"'; } ?>>
                            <th><label for="time_limit_delete_posts"><?php _e('Time limitation for deleting posts (in minutes):', 'asgaros-forum'); ?></label></th>
                            <td>
                                <input type="number" name="time_limit_delete_posts" id="time_limit_delete_posts" value="<?php echo stripslashes($asgarosforum->options['time_limit_delete_posts']); ?>" size="3" min="0">
                                <span class="description"><?php _e('(0 = No limitation)', 'asgaros-forum'); ?></span>
                            </td>
                        </tr>

                        <tr>
                            <th><label for="enable_delete_topic"><?php _e('Users can delete their own topics', 'asgaros-forum'); ?></label></th>
                            <td><input type="checkbox" name="enable_delete_topic" id="enable_delete_topic" class="show_hide_initiator" data-hide-class="delete-topic-option" <?php checked(!empty($asgarosforum->options['enable_delete_topic'])); ?>></td>
                        </tr>

                        <?php
                        $delete_topic_option = checked(!empty($asgarosforum->options['enable_delete_topic']), true, false);
                        ?>

                        <tr class="delete-topic-option" <?php if (!$delete_topic_option) { echo 'style="display: none;"'; } ?>>
                            <th><label for="time_limit_delete_topics"><?php _e('Time limitation for deleting topics (in minutes):', 'asgaros-forum'); ?></label></th>
                            <td>
                                <input type="number" name="time_limit_delete_topics" id="time_limit_delete_topics" value="<?php echo stripslashes($asgarosforum->options['time_limit_delete_topics']); ?>" size="3" min="0">
                                <span class="description"><?php _e('(0 = No limitation)', 'asgaros-forum'); ?></span>
                            </td>
                        </tr>
                    </table>
                </div>

                <?php $display = ($selected_tab == 'breadcrumbs') ? 'block' : 'none'; ?>
                <div class="tab" id="tab-breadcrumbs" style="display: <?php echo $display; ?>;">
                    <div class="settings-header">
                        <span class="fas fa-map-marked"></span>
                        <?php _e('Breadcrumbs', 'asgaros-forum'); ?>
                    </div>
                    <?php
                    $breadcrumbs_option = checked(!empty($asgarosforum->options['enable_breadcrumbs']), true, false);
                    ?>
                    <table>
                        <tr>
                            <th><label for="enable_breadcrumbs"><?php _e('Enable breadcrumbs', 'asgaros-forum'); ?></label></th>
                            <td><input type="checkbox" name="enable_breadcrumbs" id="enable_breadcrumbs" class="show_hide_initiator" data-hide-class="breadcrumbs-option" <?php checked(!empty($asgarosforum->options['enable_breadcrumbs'])); ?>></td>
                        </tr>
                        <tr class="breadcrumbs-option" <?php if (!$breadcrumbs_option) { echo 'style="display: none;"'; } ?>>
                            <th><label for="breadcrumbs_show_category"><?php _e('Show category name in breadcrumbs', 'asgaros-forum'); ?></label></th>
                            <td><input type="checkbox" name="breadcrumbs_show_category" id="breadcrumbs_show_category" <?php checked(!empty($asgarosforum->options['breadcrumbs_show_category'])); ?>></td>
                        </tr>
                    </table>
                </div>

                <?php $display = ($selected_tab == 'notifications') ? 'block' : 'none'; ?>
                <div class="tab" id="tab-notifications" style="display: <?php echo $display; ?>;">
                    <div class="settings-header">
                        <span class="fas fa-envelope"></span>
                        <?php _e('Notifications', 'asgaros-forum'); ?>
                    </div>
                    <table>
                        <tr>
                            <th><label for="notification_sender_name"><?php _e('Sender name:', 'asgaros-forum'); ?></label></th>
                            <td><input class="regular-text" type="text" name="notification_sender_name" id="notification_sender_name" value="<?php echo esc_html(stripslashes($asgarosforum->options['notification_sender_name'])); ?>"></td>
                        </tr>
                        <tr>
                            <th><label for="notification_sender_mail"><?php _e('Sender mail:', 'asgaros-forum'); ?></label></th>
                            <td><input class="regular-text" type="text" name="notification_sender_mail" id="notification_sender_mail" value="<?php echo esc_html(stripslashes($asgarosforum->options['notification_sender_mail'])); ?>"></td>
                        </tr>
                        <tr>
                            <th>
                                <label for="receivers_admin_notifications"><?php _e('Receivers of administrative notifications:', 'asgaros-forum'); ?></label>
                                <span class="description"><?php _e('A comma-separated list of mail-addresses which can receive administrative notifications (new reports, unapproved topics, and more).', 'asgaros-forum'); ?></span>
                            </th>
                            <td><input class="regular-text" type="text" name="receivers_admin_notifications" id="receivers_admin_notifications" value="<?php echo esc_html(stripslashes($asgarosforum->options['receivers_admin_notifications'])); ?>"></td>
                        </tr>
                        <tr>
                            <th><label for="allow_subscriptions">
                                <?php _e('Enable subscriptions', 'asgaros-forum'); ?></label>
                                <span class="description"><?php _e('The subscription-functionality is only available for logged-in users.', 'asgaros-forum'); ?></span>
                            </th>
                            <td><input type="checkbox" name="allow_subscriptions" id="allow_subscriptions" <?php checked(!empty($asgarosforum->options['allow_subscriptions'])); ?>></td>
                        </tr>
                        <tr>
                            <th><label for="admin_subscriptions"><?php _e('Notify receivers of administrative notifications about new topics', 'asgaros-forum'); ?></label></th>
                            <td><input type="checkbox" name="admin_subscriptions" id="admin_subscriptions" <?php checked(!empty($asgarosforum->options['admin_subscriptions'])); ?>></td>
                        </tr>
                        <!-- New Post Notifications -->
                        <tr>
                            <th><label for="mail_template_new_post_subject"><?php _e('New post notification subject:', 'asgaros-forum'); ?></label></th>
                            <td><input class="regular-text" type="text" name="mail_template_new_post_subject" id="mail_template_new_post_subject" value="<?php echo esc_html(stripslashes($asgarosforum->options['mail_template_new_post_subject'])); ?>"></td>
                        </tr>
                        <tr>
                            <th><label for="mail_template_new_post_message"><?php _e('New post notification message:', 'asgaros-forum'); ?></label></th>
                            <td><textarea class="large-text" rows="8" cols="80" type="text" name="mail_template_new_post_message" id="mail_template_new_post_message"><?php echo esc_html(stripslashes($asgarosforum->options['mail_template_new_post_message'])); ?></textarea></td>
                        </tr>
                        <!-- New Topic Notifications -->
                        <tr>
                            <th><label for="mail_template_new_topic_subject"><?php _e('New topic notification subject:', 'asgaros-forum'); ?></label></th>
                            <td><input class="regular-text" type="text" name="mail_template_new_topic_subject" id="mail_template_new_topic_subject" value="<?php echo esc_html(stripslashes($asgarosforum->options['mail_template_new_topic_subject'])); ?>"></td>
                        </tr>
                        <tr>
                            <th><label for="mail_template_new_topic_message"><?php _e('New topic notification message:', 'asgaros-forum'); ?></label></th>
                            <td><textarea class="large-text" rows="8" cols="80" type="text" name="mail_template_new_topic_message" id="mail_template_new_topic_message"><?php echo esc_html(stripslashes($asgarosforum->options['mail_template_new_topic_message'])); ?></textarea></td>
                        </tr>
                    </table>
                </div>

                <?php $display = ($selected_tab == 'mentioning') ? 'block' : 'none'; ?>
                <div class="tab" id="tab-mentioning" style="display: <?php echo $display; ?>;">
                    <div class="settings-header">
                        <span class="fas fa-at"></span>
                        <?php _e('Mentioning', 'asgaros-forum'); ?>
                    </div>
                    <?php
                    $mentioning_option = checked(!empty($asgarosforum->options['enable_mentioning']), true, false);
                    ?>
                    <table>
                        <tr>
                            <th><label for="enable_mentioning"><?php _e('Enable Mentioning', 'asgaros-forum'); ?></label></th>
                            <td><input type="checkbox" name="enable_mentioning" id="enable_mentioning" class="show_hide_initiator" data-hide-class="mentioning-option" <?php checked(!empty($asgarosforum->options['enable_mentioning'])); ?>></td>
                        </tr>
                        <tr class="mentioning-option" <?php if (!$mentioning_option) { echo 'style="display: none;"'; } ?>>
                            <th><label for="mail_template_mentioned_subject"><?php _e('Mentioning notification subject:', 'asgaros-forum'); ?></label></th>
                            <td><input class="regular-text" type="text" name="mail_template_mentioned_subject" id="mail_template_mentioned_subject" value="<?php echo esc_html(stripslashes($asgarosforum->options['mail_template_mentioned_subject'])); ?>"></td>
                        </tr>
                        <tr class="mentioning-option" <?php if (!$mentioning_option) { echo 'style="display: none;"'; } ?>>
                            <th><label for="mail_template_mentioned_message"><?php _e('Mentioning notification message:', 'asgaros-forum'); ?></label></th>
                            <td><textarea class="large-text" rows="8" cols="80" type="text" name="mail_template_mentioned_message" id="mail_template_mentioned_message"><?php echo esc_html(stripslashes($asgarosforum->options['mail_template_mentioned_message'])); ?></textarea></td>
                        </tr>
                    </table>
                </div>

                <?php $display = ($selected_tab == 'memberslist') ? 'block' : 'none'; ?>
                <div class="tab" id="tab-memberslist" style="display: <?php echo $display; ?>;">
                    <div class="settings-header">
                        <span class="fas fa-users"></span>
                        <?php _e('Members List', 'asgaros-forum'); ?>
                    </div>
                    <?php
                    $membersListOption = checked(!empty($asgarosforum->options['enable_memberslist']), true, false);
                    ?>
                    <table>
                        <tr>
                            <th><label for="enable_memberslist"><?php _e('Enable members list', 'asgaros-forum'); ?></label></th>
                            <td><input type="checkbox" name="enable_memberslist" id="enable_memberslist" class="show_hide_initiator" data-hide-class="memberslist-option" <?php checked(!empty($asgarosforum->options['enable_memberslist'])); ?>></td>
                        </tr>
                        <tr class="memberslist-option" <?php if (!$membersListOption) { echo 'style="display: none;"'; } ?>>
                            <th><label for="memberslist_loggedin_only"><?php _e('Show members list to logged-in users only', 'asgaros-forum'); ?></label></th>
                            <td><input type="checkbox" name="memberslist_loggedin_only" id="memberslist_loggedin_only" <?php checked(!empty($asgarosforum->options['memberslist_loggedin_only'])); ?>></td>
                        </tr>
                        <tr class="memberslist-option" <?php if (!$membersListOption) { echo 'style="display: none;"'; } ?>>
                            <th><label for="members_per_page"><?php _e('Members per page:', 'asgaros-forum'); ?></label></th>
                            <td><input type="number" name="members_per_page" id="members_per_page" value="<?php echo stripslashes($asgarosforum->options['members_per_page']); ?>" size="3" min="1"></td>
                        </tr>
                    </table>
                </div>

                <?php $display = ($selected_tab == 'profiles') ? 'block' : 'none'; ?>
                <div class="tab" id="tab-profiles" style="display: <?php echo $display; ?>;">
                    <div class="settings-header">
                        <span class="fas fa-user"></span>
                        <?php _e('Profiles', 'asgaros-forum'); ?>
                    </div>
                    <?php
                    $profileOption = checked(!empty($asgarosforum->options['enable_profiles']), true, false);
                    ?>
                    <table>
                        <tr>
                            <th><label for="enable_profiles"><?php _e('Enable profiles', 'asgaros-forum'); ?></label></th>
                            <td><input type="checkbox" name="enable_profiles" id="enable_profiles" class="show_hide_initiator" data-hide-class="profile-option" <?php checked(!empty($asgarosforum->options['enable_profiles'])); ?>></td>
                        </tr>
                        <tr class="profile-option" <?php if (!$profileOption) { echo 'style="display: none;"'; } ?>>
                            <th><label for="hide_profiles_from_guests"><?php _e('Show profiles to logged-in users only', 'asgaros-forum'); ?></label></th>
                            <td><input type="checkbox" name="hide_profiles_from_guests" id="hide_profiles_from_guests" <?php checked(!empty($asgarosforum->options['hide_profiles_from_guests'])); ?>></td>
                        </tr>
                    </table>
                </div>

                <?php $display = ($selected_tab == 'uploads') ? 'block' : 'none'; ?>
                <div class="tab" id="tab-uploads" style="display: <?php echo $display; ?>;">
                    <div class="settings-header">
                        <span class="fas fa-upload"></span>
                        <?php _e('Uploads', 'asgaros-forum'); ?>
                    </div>
                    <?php
                    $uploadsOption = checked(!empty($asgarosforum->options['allow_file_uploads']), true, false);
                    ?>
                    <table>
                        <tr>
                            <th><label for="allow_file_uploads"><?php _e('Allow uploads', 'asgaros-forum'); ?></label></th>
                            <td><input type="checkbox" name="allow_file_uploads" id="allow_file_uploads" class="show_hide_initiator" data-hide-class="uploads-option" <?php echo $uploadsOption; ?>></td>
                        </tr>
                        <tr class="uploads-option" <?php if (!$uploadsOption) { echo 'style="display: none;"'; } ?>>
                            <th><label for="uploads_show_thumbnails"><?php _e('Show thumbnails', 'asgaros-forum'); ?></label></th>
                            <td><input type="checkbox" name="uploads_show_thumbnails" id="uploads_show_thumbnails" <?php checked(!empty($asgarosforum->options['uploads_show_thumbnails'])); ?>></td>
                        </tr>
                        <tr class="uploads-option" <?php if (!$uploadsOption) { echo 'style="display: none;"'; } ?>>
                            <th><label for="hide_uploads_from_guests"><?php _e('Show uploaded files to logged-in users only', 'asgaros-forum'); ?></label></th>
                            <td><input type="checkbox" name="hide_uploads_from_guests" id="hide_uploads_from_guests" <?php checked(!empty($asgarosforum->options['hide_uploads_from_guests'])); ?>></td>
                        </tr>
                        <tr class="uploads-option" <?php if (!$uploadsOption) { echo 'style="display: none;"'; } ?>>
                            <th><label for="upload_permission"><?php _e('Who can upload files:', 'asgaros-forum'); ?></label></th>
                            <td>
                                <select name="upload_permission" id="upload_permission">';
                                    <option value="everyone" <?php if ($asgarosforum->options['upload_permission'] == 'everyone') { echo 'selected="selected"'; } ?>><?php _e('Everyone', 'asgaros-forum'); ?></option>
                                    <option value="loggedin" <?php if ($asgarosforum->options['upload_permission'] == 'loggedin') { echo 'selected="selected"'; } ?>><?php _e('Logged in users only', 'asgaros-forum'); ?></option>
                                    <option value="moderator" <?php if ($asgarosforum->options['upload_permission'] == 'moderator') { echo 'selected="selected"'; } ?>><?php _e('Moderators only', 'asgaros-forum'); ?></option>
                                </select>
                            </td>
                        </tr>
                        <tr class="uploads-option" <?php if (!$uploadsOption) { echo 'style="display: none;"'; } ?>>
                            <th><label for="allowed_filetypes"><?php _e('Allowed filetypes:', 'asgaros-forum'); ?></label></th>
                            <td><input class="regular-text" type="text" name="allowed_filetypes" id="allowed_filetypes" value="<?php echo esc_html(stripslashes($asgarosforum->options['allowed_filetypes'])); ?>"></td>
                        </tr>
                        <tr class="uploads-option" <?php if (!$uploadsOption) { echo 'style="display: none;"'; } ?>>
                            <th><label for="uploads_maximum_number"><?php _e('Maximum files per post:', 'asgaros-forum'); ?></label></th>
                            <td>
                                <input type="number" name="uploads_maximum_number" id="uploads_maximum_number" value="<?php echo stripslashes($asgarosforum->options['uploads_maximum_number']); ?>" size="3" min="0">
                                <span class="description"><?php _e('(0 = No limitation)', 'asgaros-forum'); ?></span>
                            </td>
                        </tr>
                        <tr class="uploads-option" <?php if (!$uploadsOption) { echo 'style="display: none;"'; } ?>>
                            <th><label for="uploads_maximum_size"><?php _e('Maximum file size (in megabyte):', 'asgaros-forum'); ?></label></th>
                            <td>
                                <input type="number" name="uploads_maximum_size" id="uploads_maximum_size" value="<?php echo stripslashes($asgarosforum->options['uploads_maximum_size']); ?>" size="3" min="0">
                                <span class="description"><?php _e('(0 = No limitation)', 'asgaros-forum'); ?></span>
                            </td>
                        </tr>
                    </table>
                </div>

                <?php $display = ($selected_tab == 'reports') ? 'block' : 'none'; ?>
                <div class="tab" id="tab-reports" style="display: <?php echo $display; ?>;">
                    <div class="settings-header">
                        <span class="fas fa-exclamation-triangle"></span>
                        <?php _e('Reports', 'asgaros-forum'); ?>
                    </div>
                    <?php
                    $reportsOption = checked(!empty($asgarosforum->options['reports_enabled']), true, false);
                    ?>
                    <table>
                        <tr>
                            <th><label for="reports_enabled"><?php _e('Enable reports', 'asgaros-forum'); ?></label></th>
                            <td><input type="checkbox" name="reports_enabled" id="reports_enabled" class="show_hide_initiator" data-hide-class="reports-option" <?php checked(!empty($asgarosforum->options['reports_enabled'])); ?>></td>
                        </tr>
                        <tr class="reports-option" <?php if (!$reportsOption) { echo 'style="display: none;"'; } ?>>
                            <th><label for="reports_notifications"><?php _e('Notify receivers of administrative notifications about new reports', 'asgaros-forum'); ?></label></th>
                            <td><input type="checkbox" name="reports_notifications" id="reports_notifications" <?php checked(!empty($asgarosforum->options['reports_notifications'])); ?>></td>
                        </tr>
                    </table>
                </div>

                <?php $display = ($selected_tab == 'signatures') ? 'block' : 'none'; ?>
                <div class="tab" id="tab-signatures" style="display: <?php echo $display; ?>;">
                    <div class="settings-header">
                        <span class="fas fa-signature"></span>
                        <?php _e('Signatures', 'asgaros-forum'); ?>
                    </div>
                    <?php
                    $signaturesOption = checked(!empty($asgarosforum->options['allow_signatures']), true, false);
                    ?>
                    <table>
                        <tr>
                            <th><label for="allow_signatures"><?php _e('Enable signatures', 'asgaros-forum'); ?></label></th>
                            <td><input type="checkbox" name="allow_signatures" id="allow_signatures" class="show_hide_initiator" data-hide-class="signatures-option" <?php checked(!empty($asgarosforum->options['allow_signatures'])); ?>></td>
                        </tr>
                        <tr class="signatures-option" <?php if (!$signaturesOption) { echo 'style="display: none;"'; } ?>>
                            <th><label for="signatures_permission"><?php _e('Who can use signatures:', 'asgaros-forum'); ?></label></th>
                            <td>
                                <select name="signatures_permission" id="signatures_permission">';
                                    <option value="loggedin" <?php if ($asgarosforum->options['signatures_permission'] == 'loggedin') { echo 'selected="selected"'; } ?>><?php _e('Logged in users & Moderators', 'asgaros-forum'); ?></option>
                                    <option value="moderator" <?php if ($asgarosforum->options['signatures_permission'] == 'moderator') { echo 'selected="selected"'; } ?>><?php _e('Moderators only', 'asgaros-forum'); ?></option>
                                </select>
                            </td>
                        </tr>
                        <tr class="signatures-option" <?php if (!$signaturesOption) { echo 'style="display: none;"'; } ?>>
                            <th><label for="signatures_html_allowed"><?php _e('Allow HTML tags in signatures', 'asgaros-forum'); ?></label></th>
                            <td><input type="checkbox" name="signatures_html_allowed" id="signatures_html_allowed" <?php checked(!empty($asgarosforum->options['signatures_html_allowed'])); ?>></td>
                        </tr>
                        <tr class="signatures-option" <?php if (!$signaturesOption) { echo 'style="display: none;"'; } ?>>
                            <th><label for="signatures_html_tags"><?php _e('Allowed HTML tags:', 'asgaros-forum'); ?></label></th>
                            <td><input class="regular-text" type="text" name="signatures_html_tags" id="signatures_html_tags" value="<?php echo esc_html(stripslashes($asgarosforum->options['signatures_html_tags'])); ?>"></td>
                        </tr>
                    </table>
                </div>

                <?php $display = ($selected_tab == 'activity') ? 'block' : 'none'; ?>
                <div class="tab" id="tab-activity" style="display: <?php echo $display; ?>;">
                    <div class="settings-header">
                        <span class="fas fa-bullhorn"></span>
                        <?php _e('Activity', 'asgaros-forum'); ?>
                    </div>
                    <?php
                    $activityOption = checked(!empty($asgarosforum->options['enable_activity']), true, false);
                    ?>
                    <table>
                        <tr>
                            <th><label for="enable_activity"><?php _e('Enable Activity Feed', 'asgaros-forum'); ?></label></th>
                            <td><input type="checkbox" name="enable_activity" id="enable_activity" class="show_hide_initiator" data-hide-class="activity-option" <?php checked(!empty($asgarosforum->options['enable_activity'])); ?>></td>
                        </tr>
                        <tr class="activity-option" <?php if (!$activityOption) { echo 'style="display: none;"'; } ?>>
                            <th><label for="activity_days"><?php _e('Days of activity to show:', 'asgaros-forum'); ?></label></th>
                            <td><input type="number" name="activity_days" id="activity_days" value="<?php echo stripslashes($asgarosforum->options['activity_days']); ?>" size="3" min="1"></td>
                        </tr>
                        <tr class="activity-option" <?php if (!$activityOption) { echo 'style="display: none;"'; } ?>>
                            <th><label for="activities_per_page"><?php _e('Activities per page:', 'asgaros-forum'); ?></label></th>
                            <td><input type="number" name="activities_per_page" id="activities_per_page" value="<?php echo stripslashes($asgarosforum->options['activities_per_page']); ?>" size="3" min="1"></td>
                        </tr>
                    </table>
                </div>

                <?php $display = ($selected_tab == 'ads') ? 'block' : 'none'; ?>
                <div class="tab" id="tab-ads" style="display: <?php echo $display; ?>;">
                    <div class="settings-header">
                        <span class="fas fa-ad"></span>
                        <?php _e('Ads', 'asgaros-forum'); ?>
                    </div>
                    <?php
                    $adsOption = checked(!empty($asgarosforum->options['enable_ads']), true, false);
                    ?>
                    <table>
                        <tr>
                            <th><label for="enable_ads"><?php _e('Enable ads', 'asgaros-forum'); ?></label></th>
                            <td><input type="checkbox" name="enable_ads" id="enable_ads" class="show_hide_initiator" data-hide-class="ads-option" <?php checked(!empty($asgarosforum->options['enable_ads'])); ?>></td>
                        </tr>
                        <tr class="ads-option" <?php if (!$adsOption) { echo 'style="display: none;"'; } ?>>
                            <th><label for="ads_frequency_categories"><?php _e('Ad frequency for categories:', 'asgaros-forum'); ?></label></th>
                            <td><input type="number" name="ads_frequency_categories" id="ads_frequency_categories" value="<?php echo stripslashes($asgarosforum->options['ads_frequency_categories']); ?>" size="3" min="1"></td>
                        </tr>
                        <tr class="ads-option" <?php if (!$adsOption) { echo 'style="display: none;"'; } ?>>
                            <th><label for="ads_frequency_forums"><?php _e('Ad frequency for forums:', 'asgaros-forum'); ?></label></th>
                            <td><input type="number" name="ads_frequency_forums" id="ads_frequency_forums" value="<?php echo stripslashes($asgarosforum->options['ads_frequency_forums']); ?>" size="3" min="1"></td>
                        </tr>
                        <tr class="ads-option" <?php if (!$adsOption) { echo 'style="display: none;"'; } ?>>
                            <th><label for="ads_frequency_topics"><?php _e('Ad frequency for topics:', 'asgaros-forum'); ?></label></th>
                            <td><input type="number" name="ads_frequency_topics" id="ads_frequency_topics" value="<?php echo stripslashes($asgarosforum->options['ads_frequency_topics']); ?>" size="3" min="1"></td>
                        </tr>
                        <tr class="ads-option" <?php if (!$adsOption) { echo 'style="display: none;"'; } ?>>
                            <th><label for="ads_frequency_posts"><?php _e('Ad frequency for posts:', 'asgaros-forum'); ?></label></th>
                            <td><input type="number" name="ads_frequency_posts" id="ads_frequency_posts" value="<?php echo stripslashes($asgarosforum->options['ads_frequency_posts']); ?>" size="3" min="1"></td>
                        </tr>
                    </table>
                </div>

                <?php $display = ($selected_tab == 'polls') ? 'block' : 'none'; ?>
                <div class="tab" id="tab-polls" style="display: <?php echo $display; ?>;">
                    <div class="settings-header">
                        <span class="fas fa-poll-h"></span>
                        <?php _e('Polls', 'asgaros-forum'); ?>
                    </div>
                    <?php
                    $polls_option = checked(!empty($asgarosforum->options['enable_polls']), true, false);
                    ?>
                    <table>
                        <tr>
                            <th><label for="enable_polls"><?php _e('Enable Polls', 'asgaros-forum'); ?></label></th>
                            <td><input type="checkbox" name="enable_polls" id="enable_polls" class="show_hide_initiator" data-hide-class="polls-option" <?php checked(!empty($asgarosforum->options['enable_polls'])); ?>></td>
                        </tr>

                        <tr class="polls-option" <?php if (!$polls_option) { echo 'style="display: none;"'; } ?>>
                            <th><label for="polls_permission"><?php _e('Who can create polls:', 'asgaros-forum'); ?></label></th>
                            <td>
                                <select name="polls_permission" id="polls_permission">';
                                    <option value="loggedin" <?php if ($asgarosforum->options['polls_permission'] == 'loggedin') { echo 'selected="selected"'; } ?>><?php _e('Logged in users only', 'asgaros-forum'); ?></option>
                                    <option value="moderator" <?php if ($asgarosforum->options['polls_permission'] == 'moderator') { echo 'selected="selected"'; } ?>><?php _e('Moderators only', 'asgaros-forum'); ?></option>
                                </select>
                            </td>
                        </tr>
                    </table>
                </div>

                <?php $display = ($selected_tab == 'spoilers') ? 'block' : 'none'; ?>
                <div class="tab" id="tab-spoilers" style="display: <?php echo $display; ?>;">
                    <div class="settings-header">
                        <span class="fas fa-eye-slash"></span>
                        <?php _e('Spoilers', 'asgaros-forum'); ?>
                    </div>
                    <?php
                    $spoilers_option = checked(!empty($asgarosforum->options['enable_spoilers']), true, false);
                    ?>
                    <table>
                        <tr>
                            <th><label for="enable_spoilers"><?php _e('Enable spoilers', 'asgaros-forum'); ?></label></th>
                            <td><input type="checkbox" name="enable_spoilers" id="enable_spoilers" class="show_hide_initiator" data-hide-class="spoilers-option" <?php checked(!empty($asgarosforum->options['enable_spoilers'])); ?>></td>
                        </tr>
                        <tr class="spoilers-option" <?php if (!$spoilers_option) { echo 'style="display: none;"'; } ?>>
                            <th><label for="hide_spoilers_from_guests"><?php _e('Hide spoilers from logged-out users', 'asgaros-forum'); ?></label></th>
                            <td><input type="checkbox" name="hide_spoilers_from_guests" id="hide_spoilers_from_guests" <?php checked(!empty($asgarosforum->options['hide_spoilers_from_guests'])); ?>></td>
                        </tr>
                    </table>
                </div>

                <input type="submit" name="af_options_submit" class="button button-primary" value="<?php _e('Save Settings', 'asgaros-forum'); ?>">
            </div>

            <div class="clear"></div>
        </div>
    </form>
</div>
