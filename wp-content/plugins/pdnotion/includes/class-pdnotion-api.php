<?php

class PDNotion_API {

    private $api_key;

    public function __construct() {
        // Fetch the Integration Secret as the API Key from WordPress settings.
        $this->api_key = get_option( 'pdnotion_api_key' );

        // Ensure the API key is set.
        if ( empty( $this->api_key ) ) {
            error_log( 'Notion API Key is missing. Please configure it in the PDNotion settings.' );
        }
    }

    /**
     * Fetch data from a Notion database.
     *
     * @param string $database_id The ID of the Notion database.
     * @return array|WP_Error Response data or error.
     */
    public function fetch_notion_data( $database_id ) {
        if ( empty( $database_id ) ) {
            return new WP_Error( 'missing_database_id', 'Database ID is required.', [ 'status' => 400 ] );
        }

        $url = "https://api.notion.com/v1/databases/$database_id/query";

        $response = wp_remote_post( $url, [
            'headers' => [
                'Authorization' => 'Bearer ' . $this->api_key,
                'Notion-Version' => '2022-06-28',
            ],
        ]);

        // Handle HTTP request errors.
        if ( is_wp_error( $response ) ) {
            error_log( 'Notion API request error: ' . $response->get_error_message() );
            return new WP_Error( 'api_request_failed', 'Unable to contact Notion API.', [ 'status' => 500 ] );
        }

        // Decode and return the API response body.
        $body = wp_remote_retrieve_body( $response );
        $data = json_decode( $body, true );

        // Check for valid JSON response.
        if ( json_last_error() !== JSON_ERROR_NONE ) {
            error_log( 'Failed to decode Notion API response: ' . json_last_error_msg() );
            return new WP_Error( 'invalid_json', 'Failed to decode Notion API response.', [ 'status' => 500 ] );
        }

        return $data;
    }
}
