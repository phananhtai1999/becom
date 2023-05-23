<?php
$con = mysqli_connect("localhost","root","","send_email");
if (mysqli_connect_errno()) {
    echo "Failed to connect to MySQL: " . mysqli_connect_error();
    exit();
}
$asset = mysqli_query($con, 'select * from assets where uuid = '. $_GET['as']);
$assetSize = mysqli_query($con, 'select * from asset_sizes where uuid = '. $asset->fetch_assoc()['uuid']);
$assetSizeRow = $assetSize->fetch_array();
echo 'function ShowBanners() {
        const image = document.createElement("img");
        image.setAttribute("src", "https://www.simplilearn.com/ice9/free_resources_article_thumb/what_is_image_Processing.jpg");
        image.setAttribute("height", "'. $assetSizeRow['height'] .'");
        image.setAttribute("width", "'. $assetSizeRow['width'] .'");
        const link = document.createElement("a");
        link.href = "' . $_GET['link'] .'";
        link.appendChild(image);
        document.getElementById("banner-ads").appendChild(link);
    }';
