<?php
class TessituraImporter
{

    public static function checkConfig()
    {
        if(defined('TESSITURA_REST_URL') && is_string(TESSITURA_REST_URL) && !empty(TESSITURA_REST_URL)) {
            //
        } else {
            throw new Exception('Invalid Tessitura URL');
        }

        if(defined('TESSITURA_REST_CREDS') && is_string(TESSITURA_REST_CREDS) && !empty(TESSITURA_REST_CREDS)) {
            //
        } else {
            throw new Exception('Invalid Tessitura Credentials');
        }

        return true;
    }

    protected static function createEvent(array $eventData)
    {
        if(class_exists('Tribe__Events__Main') && function_exists('tribe_create_event')) {
            // Create event
            $eventId = tribe_create_event($eventData);
            if($eventId) {
                return $eventId;
            } else {
                throw new Exception('Event creation failed for performanceId: '.$eventData['tessitura_production_season_id']);
            }
        } else {
            throw new Exception('Events Calendar Pro not loaded');
        }
    }

    protected static function restCall(string $endpoint)
    {   
        $url = TESSITURA_REST_URL . $endpoint;
        $args = array(
            'headers' => array(
                'Authorization' => 'Basic ' . base64_encode( TESSITURA_REST_CREDS )
            )
        );
        $data = wp_remote_get( $url, $args );
        if($data instanceof WP_Error) {
            throw new Exception($data->get_error_message);
        } else {
            $data = json_decode(wp_remote_retrieve_body($data), true);
        }
        return $data;
    }

    protected static function getTessituraPerformance(int $performanceId)
    {
        return self::restCall('TXN/Performances/'.$performanceId);
    }

    public static function importTessituraPerformance(int $performanceId)
    {
        $performance = self::getTessituraPerformance($performanceId);
        $eventData = [
            'post_title' => $performance['ProductionSeason']['Description'],
            'meta_input' => [
                'tessitura_production_season_id' => $performance['Id'],
                'performance_header_show_title' => $performance['ProductionSeason']['Description']
            ],
            // 'EventStartDate' => '',
            // 'EventEndDate' => '',
            // 'EventAllDay' => '',
            // 'EventStartHour' => '',
            // 'EventStartMinute' => '',
            // 'EventEndHour' => '',
            // 'EventEndMinute' => '',
            // 'EventCost' => '',
            // 'Veune' => [],
            // 'Organizer' => []
        ];
        $eventId = self::createEvent($eventData);
        return $eventId;
    }

    public static function importTessituraProductionSeason(int $productionSeasonId)
    {
        // 
    }
}
