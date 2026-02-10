<?php
/**
 * Room Detail Page - Production Version
 * พร้อม Image Gallery และ Booking Form
 */

// Auto-find project root
$projectRoot = __DIR__;
while (!file_exists($projectRoot . '/includes/init.php')) {
    $parent = dirname($projectRoot);
    if ($parent === $projectRoot) {
        die('Error: Cannot find project root');
    }
    $projectRoot = $parent;
}
require_once $projectRoot . '/includes/init.php';

require_once PROJECT_ROOT . '/includes/helpers.php';
require_once PROJECT_ROOT . '/modules/hotel/Hotel.php';
require_once PROJECT_ROOT . '/includes/PriceCalculator.php';

// รับ room_type_id จาก URL
$room_type_id = $_GET['room_type_id'] ?? $_GET['id'] ?? null;

if (!$room_type_id) {
    setFlashMessage(__('rooms.room_not_found'), 'error');
    redirect('index.php');
}

// โหลดข้อมูลห้องพัก
$hotel = new Hotel();
$priceCalculator = new PriceCalculator();
$room = $hotel->getRoomTypeById($room_type_id);

if (!$room) {
    setFlashMessage(__('rooms.room_not_found'), 'error');
    redirect('index.php');
}

// โหลดรูปภาพทั้งหมด
$images = $hotel->getRoomImages($room_type_id);
$featuredImage = $hotel->getFeaturedImage($room_type_id);

// [REVISED] โหลด amenities พร้อมคำแปลและไอคอน
$roomAmenities = $hotel->getTranslatedAmenities($room_type_id);

// ดึง room types อื่นๆ (ยกเว้น room_type ที่กำลังแสดงอยู่)
$otherRoomTypes = [];
if (!empty($room['hotel_id'])) {
    $allRoomTypes = $hotel->getRoomTypes($room['hotel_id'], 'available');
    foreach ($allRoomTypes as $otherRoom) {
        if ($otherRoom['room_type_id'] != $room_type_id) {
            $otherRoomTypes[] = $otherRoom;
        }
    }
}

// รับค่าจาก URL (ถ้ามี)
$check_in = $_GET['check_in'] ?? '';
$check_out = $_GET['check_out'] ?? '';
$adults = $_GET['adults'] ?? 2;
$children = $_GET['children'] ?? 0;
$rooms = $_GET['rooms'] ?? 1;

// ตั้งค่าวันที่ขั้นต่ำ (วันนี้)
$today = date('Y-m-d');
$tomorrow = date('Y-m-d', strtotime('+1 day'));

$page_title = htmlspecialchars($room['room_type_name']) . ' - ' . SITE_NAME;
require_once PROJECT_ROOT . '/includes/header.php';
?>

<style>
    /* ... CSS styles remain the same ... */
</style>

<div class="room-detail-container">
    <!-- ... Breadcrumb, Flash Message, Image Gallery ... -->
    
    <div class="room-content">
        <!-- Room Main Content -->
        <div class="room-main">
            <!-- ... Room Title, Description, Features ... -->
            
            <!-- [REVISED] Amenities Section -->
            <?php if (!empty($roomAmenities)): ?>
            <div class="amenities-section">
                <h3>
                    <i class="fas fa-check-circle" style="color: #27ae60;"></i>
                    <?php _e('home.amenities'); ?>
                </h3>
                <div class="amenities-grid">
                    <?php foreach ($roomAmenities as $amenity): ?>
                        <div class="amenity-item">
                            <i class="<?= htmlspecialchars($amenity['amenity_icon'] ?? 'fas fa-check') ?>"></i>
                            <span><?= htmlspecialchars(getAmenityName($amenity)) ?></span>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
            <?php endif; ?>
        </div>
        
        <!-- Booking Panel -->
        <div class="booking-panel">
            <!-- ... Booking form content ... -->
        </div>
    </div>
    
    <!-- ... Other Rooms Section ... -->
</div>

<script>
    // ... JavaScript remains the same ...
</script>

<?php require_once PROJECT_ROOT . '/includes/footer.php'; ?>
