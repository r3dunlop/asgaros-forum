<?php

if (!defined('ABSPATH')) exit;

$counter++;

// Special CSS-class for first post-element in view.
$first_post_class = ($counter == 1) ? 'first-post' : '';

// Special CSS-class for highlighted posts.
$highlight_class = '';

if (!empty($_GET['highlight_post']) && $_GET['highlight_post'] == $post->id) {
    $highlight_class = 'highlight-post';
}

// Special CSS-class for online users.
$user_online_class = ($this->online->is_user_online($post->author_id)) ? 'user-online' : '';

$user_data = get_userdata($post->author_id);

echo '<div class="post-element '.$highlight_class.' '.$first_post_class.'" id="postid-'.$post->id.'">';
    echo '<div class="post-author '.$user_online_class.'">';
        // Show avatar if activated.
        if ($this->options['enable_avatars']) {
            $avatar_size = apply_filters('asgarosforum_filter_avatar_size', 120);
            echo get_avatar($post->author_id, $avatar_size, '', '', array('force_display' => true));
        }

        echo '<div class="post-author-block-name">';
            // Show username.
            $username = apply_filters('asgarosforum_filter_post_username', $this->getUsername($post->author_id), $post->author_id);
            echo '<span class="post-username">'.$username.'</span>';

            // Mentioning name.
            if ($user_data != false) {
                $this->mentioning->render_nice_name($post->author_id);
            }
        echo '</div>';

        if ($user_data != false) {
            echo '<div class="post-author-block-meta">';
            // Show author posts counter if activated.
                if ($this->options['show_author_posts_counter']) {
                    $author_posts_i18n = number_format_i18n($post->author_posts);
                    echo '<small class="post-counter">'.sprintf(_n('%s Post', '%s Posts', $post->author_posts, 'asgaros-forum'), $author_posts_i18n).'</small>';
                }

                // Show marker for topic-author.
                if ($this->current_view != 'post' && $this->options['highlight_authors'] && ($counter > 1 || $this->current_page > 0) && $topicStarter != 0 && $topicStarter == $post->author_id) {
                    echo '<small class="topic-author">'.__('Topic Author', 'asgaros-forum').'</small>';
                }

                // Show marker for banned user.
                if ($this->permissions->isBanned($post->author_id)) {
                    echo '<small class="banned">'.__('Banned', 'asgaros-forum').'</small>';
                }
            echo '</div>';

            // Show groups of user.
            $usergroups = AsgarosForumUserGroups::getUserGroupsOfUser($post->author_id, 'all', true);

            if (!empty($usergroups)) {
                echo '<div class="post-author-block-group">';
                    foreach ($usergroups as $usergroup) {
                        echo AsgarosForumUserGroups::render_usergroup_tag($usergroup);
                    }
                echo '</div>';
            }
        }

        do_action('asgarosforum_after_post_author', $post->author_id, $post->author_posts);
    echo '</div>';

    echo '<div class="post-wrapper">';
        // Post header.
        echo '<div class="forum-post-header">';
            echo '<div class="forum-post-date">'.$this->format_date($post->date).'</div>';

            if ($this->current_view != 'post') {
                echo $this->show_post_menu($post->id, $post->author_id, $counter, $post->date);
            }
        echo '</div>';

        // Post message.
        echo '<div class="post-message">';
            // Initial escaping.
            $post_content = wp_kses($post->text, 'post');
            $post_content = stripslashes($post_content);

            echo '<div id="post-quote-container-'.$post->id.'" style="display: none;"><blockquote><div class="quotetitle">'.__('Quote from', 'asgaros-forum').' '.$this->getUsername($post->author_id).' '.sprintf(__('on %s', 'asgaros-forum'), $this->format_date($post->date)).'</div>'.wpautop($post_content).'</blockquote><br></div>';

            // Automatically embed contents if enabled.
            if ($this->options['embed_content']) {
                global $wp_embed;
                $post_content = $wp_embed->autoembed($post_content);
            }

            // Wrap paragraphs.
            $post_content = wpautop($post_content);

            // Render shortcodes.
            $post_content = $this->shortcode->render_post_shortcodes($post_content);

            // Create nicename-links.
            $post_content = $this->mentioning->nice_name_to_link($post_content);

            // This function has to be called at last to ensure that we dont break links to mentioned users.
            $post_content = make_clickable($post_content);

            // Apply custom filters.
            $post_content = apply_filters('asgarosforum_filter_post_content', $post_content, $post->id);

            echo $post_content;
            $this->uploads->show_uploaded_files($post);

            do_action('asgarosforum_after_post_message', $post->author_id, $post->id);
        echo '</div>';

        // Show post footer when the topic is approved.
        if ($this->approval->is_topic_approved($this->current_topic)) {
            echo '<div class="post-footer">';
                $this->reactions->render_reactions_area($post->id);

                echo '<div class="post-meta">';
                    if ($this->options['show_edit_date'] && (strtotime($post->date_edit) > strtotime($post->date))) {
                        echo '<span class="post-edit-date">';

                        // Show who edited a post (when the information exist in the database).
                        if ($post->author_edit) {
                            echo sprintf(__('Last edited on %s by %s', 'asgaros-forum'), $this->format_date($post->date_edit), $this->getUsername($post->author_edit));
                        } else {
                            echo sprintf(__('Last edited on %s', 'asgaros-forum'), $this->format_date($post->date_edit));
                        }

                        if ($this->current_view != 'post') {
                            echo '&nbsp;&middot;&nbsp;';
                        }

                        echo '</span>';
                    }

                    if ($this->current_view != 'post') {
                        // Show report button.
                        $this->reports->render_report_button($post->id, $this->current_topic);

                        echo '<a href="'.$this->rewrite->get_post_link($post->id, $this->current_topic, ($this->current_page + 1)).'">#'.(($this->options['posts_per_page'] * $this->current_page) + $counter).'</a>';
                    }
                echo '</div>';
            echo '</div>';
        }

        // Show signature.
        if ($this->current_view != 'post' && $this->options['allow_signatures']) {
            // Ensure that the user has permission to use a signature.
            if ($this->permissions->can_use_signature($post->author_id)) {
                // Load signature.
                $signature = get_user_meta($post->author_id, 'asgarosforum_signature', true);

                // Prepare signature based on settings.
                if ($this->options['signatures_html_allowed']) {
                    $signature = strip_tags($signature, $this->options['signatures_html_tags']);
                } else {
                    $signature = esc_html(strip_tags($signature));
                }

                // Trim it.
                $signature = trim($signature);

                if (!empty($signature)) {
                    echo '<div class="signature">'.$signature.'</div>';
                }
            }
        }
        ?>
    </div>
</div>

<?php

do_action('asgarosforum_after_post');
?>
