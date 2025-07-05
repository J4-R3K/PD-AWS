<?php

class PDNotion_Public {

    public function __construct() {
        // Register the shortcode when the class is initialized.
        add_shortcode( 'pdnotion_display', [ $this, 'render_notion_data' ] );

        // Enqueue styles.
        add_action( 'wp_enqueue_scripts', [ $this, 'enqueue_styles' ] );
    }

    /**
     * Enqueue the plugin's CSS file.
     */
    public function enqueue_styles() {
        wp_enqueue_style(
            'pdnotion-styles', // Unique handle for the style.
            plugins_url( 'includes/pdnotion-styles.css', dirname( __FILE__ ) ),
            [], // Dependencies.
            '1.0.0' // Version.
        );
    }

    public function render_notion_data() {
        // Fetch the database ID from plugin settings.
        $database_id = get_option( 'pdnotion_database_id' );
        if ( empty( $database_id ) ) {
            return 'No database ID configured.';
        }

        // Initialize the Notion API class and fetch data.
        $api = new PDNotion_API();
        $data = $api->fetch_notion_data( $database_id );

        // Handle errors from the API.
        if ( is_wp_error( $data ) ) {
            return 'Error fetching data: ' . $data->get_error_message();
        }

        // Check if the results exist in the response.
        if ( empty( $data['results'] ) ) {
            return 'No data found in the database.';
        }

        // Define the columns to display in the desired order.
        $columns = [
            'Incident Name',
            'Objective',
            'AI summary',
            'Tag',
            'Main Positive Outcome',
            'Main Negative Outcome',
        ];

        // Sort the data alphabetically by Incident Name.
        usort( $data['results'], function ( $a, $b ) {
            $name_a = $a['properties']['Incident Name']['title'][0]['text']['content'] ?? '';
            $name_b = $b['properties']['Incident Name']['title'][0]['text']['content'] ?? '';
            return strcasecmp( $name_a, $name_b );
        });

        // Build the table with a responsive wrapper.
        $output = '<div class="notion-content-wrapper">';
        $output .= '<table>';
        $output .= '<tr>';
        foreach ( $columns as $column ) {
            if ( $column === 'Objective' ) {
                $output .= "<th style='width: 20%;'>{$column}</th>";
            } elseif ( $column === 'AI summary' ) {
                $output .= "<th style='width: 40%;'>{$column}</th>";
            } else {
                $output .= "<th>{$column}</th>";
            }
        }
        $output .= '<th>Link</th>'; // Add a Link column explicitly.
        $output .= '</tr>';

        foreach ( $data['results'] as $item ) {
            $output .= '<tr>';
            foreach ( $columns as $column ) {
                $value = $item['properties'][ $column ] ?? null;

                // Handle different property types.
                if ( isset( $value['title'] ) ) {
                    $output .= '<td>' . esc_html( $value['title'][0]['text']['content'] ?? 'No Title' ) . '</td>';
                } elseif ( isset( $value['select'] ) ) {
                    $output .= '<td>' . esc_html( $value['select']['name'] ?? 'No Value' ) . '</td>';
                } elseif ( isset( $value['multi_select'] ) ) {
                    $multi_values = array_map( fn( $option ) => $option['name'], $value['multi_select'] );
                    $output .= '<td>' . esc_html( implode( ', ', $multi_values ) ?: 'No Value' ) . '</td>';
                } elseif ( isset( $value['rich_text'] ) ) {
                    $output .= '<td>' . esc_html( $value['rich_text'][0]['text']['content'] ?? 'No Value' ) . '</td>';
                } else {
                    $output .= '<td>No Value</td>';
                }
            }

            // Use the public URL instead of the direct Notion page URL.
            $public_url = esc_url( "https://projectdesignio.notion.site/" . str_replace('-', '', $item['id']) );
            $output .= "<td><a href='{$public_url}' target='_blank'>View</a></td>";

            $output .= '</tr>';
        }

        $output .= '</table>';
        $output .= '</div>'; // Close the responsive wrapper.
        return $output;
    }
}

// Initialize the public-facing functionality.
new PDNotion_Public();
