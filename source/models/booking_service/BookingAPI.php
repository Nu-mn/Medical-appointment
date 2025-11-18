<?php
header("Content-Type: application/json");
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, PUT, PATCH, DELETE");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");


// Import
require_once __DIR__ . "/../../database/db.php";
include_once 'BookingService.php';


$conn = connectDB("booking_db");
$bookingService = new BookingService($conn);

// Lấy method
$method = $_SERVER['REQUEST_METHOD'];

// Lấy input JSON
$input = json_decode(file_get_contents("php://input"), true);

switch($method) {

    case 'GET':
        if(isset($_GET['id'])) {
            // GET booking theo ID
            $booking = $bookingService->getById($_GET['id']);
            if($booking) {
                echo json_encode($booking);
            } else {
                http_response_code(404);
                echo json_encode(["message" => "Booking not found."]);
            }
        } elseif(isset($_GET['status'])) {
            // GET theo trạng thái
            $bookings = $bookingService->getByStatus($_GET['status']);
            echo json_encode($bookings);
        } else {
            // GET tất cả
            $bookings = $bookingService->getAll();
            echo json_encode($bookings);
        }
        break;

    case 'POST':
        // CREATE booking
        if($bookingService->create($input)) {
            http_response_code(201);
            echo json_encode(["message" => "Booking created successfully."]);
        } else {
            http_response_code(500);
            echo json_encode(["message" => "Failed to create booking."]);
        }
        break;

    // case 'PUT':
    //     // UPDATE full booking
    //     if(isset($input['booking_id']) && $bookingService->update($input['booking_id'], $input)) {
    //         echo json_encode(["message" => "Booking updated successfully."]);
    //     } else {
    //         http_response_code(400);
    //         echo json_encode(["message" => "Failed to update booking. Missing booking_id?"]);
    //     }
    //     break;

    case 'PATCH':
        // UPDATE STATUS ONLY
        if(isset($input['booking_id']) && isset($input['status'])) {
            if($bookingService->updateStatus($input['booking_id'], $input['status'])) {
                echo json_encode(["message" => "Booking status updated successfully."]);
            } else {
                http_response_code(500);
                echo json_encode(["message" => "Failed to update booking status."]);
            }
        } else {
            http_response_code(400);
            echo json_encode(["message" => "Missing booking_id or status."]);
        }
        break;

    case 'DELETE':
        // DELETE booking
        if(isset($_GET['id'])) {
            if($bookingService->delete($_GET['id'])) {
                echo json_encode(["message" => "Booking deleted successfully."]);
            } else {
                http_response_code(500);
                echo json_encode(["message" => "Failed to delete booking."]);
            }
        } else {
            http_response_code(400);
            echo json_encode(["message" => "Missing booking id."]);
        }
        break;

    default:
        http_response_code(405);
        echo json_encode(["message" => "Method not allowed"]);
        break;
}
?>
