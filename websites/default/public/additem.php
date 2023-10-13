<?php
// including session check for not letting non logged in user access this page
include('SessionCheck.php');
$medium = null;
$isFramed = null;
$type = null;
$width = null;
$material = null;
$weight = null;
// if user id is not set in session, user cannot access this page
if(!isset($_SESSION['userid'])){
	header('Location: login.php');
}
// if id is set, the title is set to edit item and specific item for update is shown
if(isset($_GET['id'])){
	$item = $allQueries->find('auction_items', 'id', $_GET['id']);
	$title = 'Edit Item';
}
else{
	$title = 'Add Item';
}
// if delete id is fetched, 
if(isset($_GET['del_id'])){
	// setting array for item deletion request
	$updCrt = [
		'id' => $_GET['del_id'],
		'delete' => 1
	];
	// the auction items data's delete column is set to 1 which means the user is requesting for their item deletion to admin
	$allQueries->update('auction_items', $updCrt, 'id');
	echo '<script>alert("Item deletion request forwarded to admin!")
	window.location.href="items.php"</script>';
}
require 'heading.php';
$categories = $allQueries->selectAll('categories');
echo '<main class="home">';
if (isset($_POST['submit'])) {
	// selecting all items from auction item
	$items = $allQueries->selectAll('auction_items');
	// setting lot number to unique 8 digit number
	$lot_number = substr(preg_replace("/[^0-9]/", "", uniqid()), 0, 8);
	// if item's lot number exists in database, make it unique by adding 1
	foreach ($items as $item) {
		if ($lot_number == $item['lot_num']) {
			$lot_number += 1;
		}
	}

	// store the data into database if these data are set
	if (isset($_POST['medium'])) {
		$medium = $_POST['medium'];
	}

	if (isset($_POST['isFramed'])) {
		$isFramed = $_POST['isFramed'];
	}

	if (isset($_POST['type'])) {
		$type = $_POST['type'];
	}

	if (isset($_POST['width'])) {
		$width = $_POST['width'];
	}

	if (isset($_POST['material'])) {
		$material = $_POST['material'];
	}

	if (isset($_POST['weight'])) {
		$weight = $_POST['weight'];
	}

	// array to store item's data
	$criteria = [
		'item_name' => trim($_POST['item_name']),
		'artist' => trim($_POST['artist']),
		'lot_num' => $lot_number,
		'year' => $_POST['prod_year'],
		'sub_classification' => $_POST['sub_classification'],
		'postedBy' => $_SESSION['userid'],
		'description' => trim($_POST['description']),
		'categoryId' => $_POST['categoryId'],
		'price' => trim($_POST['price']),
		'medium' => $medium,
		'isFramed' => $isFramed,
		'height' => trim($_POST['height']),
		'length' => trim($_POST['length']),
		'type' => $type,
		'width' => $width,
		'material' => $material,
		'weight' => $weight
	];
	// if id is get via url (for updating item's data)
	if(isset($_GET['id'])){
		$idArr = ['id' => $_GET['id']];
		$mergedCrt = array_merge($idArr, $criteria);
		// updating auction items data
		$allQueries->update('auction_items', $mergedCrt, 'id');
		// showing success message
		echo '<p style="text-align: center; font-size: 20px;">Item updated Successfully</p>';
		$item_id = $_GET['id'];
	}else{
		// inserting items data in auction items table
		$allQueries->insert('auction_items', $criteria);
		// showing success message
		echo '<p style="text-align: center; font-size: 20px;">Item added Successfully</p>';
		// getting id of last inserted product for multiple or single images insertion
		$item_id = $allQueries->getPdo()->lastInsertId();
	}
	// if image files are set
	if (isset($_FILES['image']) && !empty($_FILES['image']['name'][0])) {
		// if id is get via url
		if(isset($_GET['id'])){
			// deleting item images associated with item id
			$allQueries->delete('item_images', 'item_id', $_GET['id']);
		}
		// getting inputted images and processed for further data insertion process
		foreach ($_FILES['image']['tmp_name'] as $key => $image) {
			$fileName = $_FILES['image']['name'][$key];
			// criteria to insert image's name with item_id fetched under two different conditions
			$crt = [
				'item_id' => $item_id,
				'img_name' => $fileName
			];
			// inserting item images into database
			$allQueries->insert('item_images', $crt);
			// moving uploaded file in local directory
			$result = move_uploaded_file($_FILES['image']['tmp_name'][$key], 'images/' . $fileName);
		}
	}
} 
else {
?>
	<h2><?= $title ?></h2>
	<form method="POST" enctype="multipart/form-data" style="height: 950px;">
		<!-- input for item name insertion -->
		<label>Item Name</label><input type="text" autofocus name="item_name" <?php if (isset($_GET['id'])) {
																					echo 'value = "' . $item['item_name'] . '"';
																				} ?> required />
		<!-- input for artist name insertion -->
		<label>Artist Name</label><input type="text" name="artist" <?php if (isset($_GET['id'])) {
																		echo 'value = "' . $item['artist'] . '"';
																	} ?> required />
		<!-- input for writing year of production of the item -->
		<label>Year of Production</label><input type="date" name="prod_year" <?php if (isset($_GET['id'])) {
																					echo 'value = "' . $item['year'] . '"';
																				} ?> required />
		<!-- select tag for selecting classification -->
		<label>Classification</label>
		<select name="sub_classification" required>
			<option disabled selected value="">Choose</option>
			<?php
			$classifications = array(
				"Landscape",
				"Seascape",
				"Portrait",
				"Figure",
				"Still Life",
				"Nude",
				"Animal",
				"Abstract",
				"Other"
			);
			// if classification data is achieved via url id, or after form submission, the specific classification is selected
			foreach ($classifications as $classification) {
				$selected = isset($_GET['id']) && $item['sub_classification'] == $classification ? 'selected="selected"' : '';
				echo '<option ' . $selected . ' value="' . $classification . '">' . $classification . '</option>';
			}
			?>
		</select>
		<!-- select tag for selecting category -->
		<label>Category</label>
		<select name="categoryId" required id="categories">
			<option disabled selected value="">Choose</option>
			<?php
			// if category data is achieved via url id, or after form submission, the specific classification is selected
			foreach ($categories as $category) {
				if (isset($_GET['id'])) {
					if ($item['categoryId'] == $category['id']) {
						echo '<option selected="selected" value="' . $category['id'] . '">' . $category['name'] . '</option>';
					} else {
						echo '<option value="' . $category['id'] . '">' . $category['name'] . '</option>';
					}
				} else {
					echo '<option value="' . $category['id'] . '">' . $category['name'] . '</option>';
				}
			}
			?>
		</select>
		<!-- this div is for showing select tag for medium, material based on different cateogry id -->
		<div id="middiv">
			<label></label>
			<select id="medium" required>
			</select>
		</div>
		<!-- this div is for checking the radio button whether the item is framed or not -->
		<div id="framediv">
			<label>Frame</label>
			<input style="margin: 35px 0 0 -80px;" type="radio" name="isFramed" value="1" id="yes" <?php if (isset($_GET['id'])) {
																										echo ($item['isFramed'] == 1) ? 'checked' : '';
																									} ?> required /><span style="float: left; margin: 30px 0 0 -80px; cursor: pointer;" onclick="document.getElementById('yes').click();">Yes</span>
			<input style="margin: 35px 0 0 -20px;" type="radio" name="isFramed" value="0" id="no" <?php if (isset($_GET['id'])) {
																										echo ($item['isFramed'] == 0) ? 'checked' : '';
																									} ?> /><span style="float: left; margin: 30px 0 0 -80px; cursor: pointer;" onclick="document.getElementById('no').click();">No</span>
		</div>
		<!-- input for writing dimensions of items -->
		<label>Dimensions</label>
		<div>
			<span style="float: left; margin: 30px 10px 0 10px;">Height</span><input style="width: 50px;" type="number" name="height" <?php if (isset($_GET['id'])) {
																																			echo 'value = "' . $item['height'] . '"';
																																		} ?> required />
			<span style="float: left; margin: 30px 10px 0 10px;" id="widthspan">Width</span><input style="width: 50px;" type="number" id="width" name="width" <?php if (isset($_GET['id'])) {
																																									echo 'value = "' . $item['width'] . '"';
																																								} ?> required />
			<span style="float: left; margin: 30px 10px 0 10px;">Length</span><input style="width: 50px;" type="number" name="length" <?php if (isset($_GET['id'])) {
																																			echo 'value = "' . $item['length'] . '"';
																																		} ?> required />
		</div>
		<!-- input for writing weight (in KG) of items -->
		<div id="weightdiv">
			<label>Approx. Weight (in KG)</label>
			<input type="number" name="weight" <?php if (isset($_GET['id'])) {
													echo 'value = "' . $item['weight'] . '"';
												} ?> required />
		</div>
		<!-- estimated price of the item -->
		<label>Estimated Price</label><span style="float: left; margin: 30px 0 0 -20px;">£</span><input type="number" style="width: 50%;" <?php if (isset($_GET['id'])) {
																																				echo 'value = "' . $item['price'] . '"';
																																			} ?> name="price" required />
		<!-- description of the item -->
		<label>Description</label><textarea name="description" style="width: 50%;" required><?php if (isset($_GET['id'])) {
																								echo $item['description'];
																							} ?></textarea>
		<!-- inputting item's images -->
		<label>Item's Image(s)</label><input type="file" <?= (isset($_GET['id'])) ? '' : 'required' ?> name="image[]" accept="image/*" id="images" multiple />
		<?php
		// showing items if id is fetched via url
		if (isset($_GET['id'])) {
			$item_images = $allQueries->select('item_images', 'item_id', $_GET['id']);
			echo '<div style="margin-top: 20px; clear: both; display: flex; flex-direction: row;">';
			foreach ($item_images as $item_image) {
				echo '<img src="../images/' . $item_image['img_name'] . '" style="height: 100px; margin: 20px;">';
			}
			echo '</div>';
		}
		?>
		<input type="submit" name="submit" value="<?= (isset($_GET['id'])) ? 'Update' : 'Add' ?> Item" id="item_submit" />
	</form>
<?php
}
?>
</main>
<footer>
    &copy; Fotheby's Auction House 2023. All rights reserved.
</footer>
<script>
	$(document).ready(function() {
		$('#middiv').hide();
		$('#framediv').hide();
		$('#framediv input').prop('disabled', true);
		$('#widthspan').hide();
		$('#width').hide();
		$('#width').prop('disabled', true);
		$('#weightdiv').hide();
		$('#weightdiv input').prop('disabled', true);
		$('#middiv select').prop('disabled', true);
		var options;
		// setting options text and value for medium
		var mediumOptions2 = [{
				value: "Pencil",
				text: "Pencil"
			},
			{
				value: "Ink",
				text: "Ink"
			},
			{
				value: "Charcoal",
				text: "Charcoal"
			},
			{
				value: "Other",
				text: "Other"
			}
		];
		// setting options text and value for medium
		var mediumOptions1 = [{
				value: "Oil",
				text: "Oil"
			},
			{
				value: "Acrylic",
				text: "Acrylic"
			},
			{
				value: "Watercolor",
				text: "Watercolor"
			},
			{
				value: "Other",
				text: "Other"
			}
		];
		// setting options text and value for type
		var typeOptions = [{
				value: "0",
				text: "Black & White"
			},
			{
				value: "1",
				text: "Color"
			}
		];
		// setting options text and value for material
		var materialOptions1 = [{
				value: "Bronze",
				text: "Bronze"
			},
			{
				value: "Marble",
				text: "Marble"
			},
			{
				value: "Pewter",
				text: "Pewter"
			},
			{
				value: "Other",
				text: "Other"
			}
		];

		// setting options text and value for material
		var materialOptions2 = [{
				value: "Oak",
				text: "Oak"
			},
			{
				value: "Beach",
				text: "Beach"
			},
			{
				value: "Pine",
				text: "Pine"
			},
			{
				value: "Willow",
				text: "Willow"
			},
			{
				value: "Other",
				text: "Other"
			}
		];

		// when the categories changes from select tag.
		$(document).on('change', '#categories', function() {
			var labelText = $('#middiv label');
			var categoryValue = $(this).val();
			// disabling 'required' keyword from input tag 
			$('#middiv select').prop('disabled', false);
			// showing medium with animation
			$('#middiv').fadeIn();

			// if categoryId is either 1 or 2, then all other non required fields are hidden (also disables 'required' keyword from input tag) and required fields are shown
			if (categoryValue === "1" || categoryValue === "2") {
				$('#middiv label').html('Medium');
				$('#medium').attr('name', 'medium');
				$('#framediv input').prop('disabled', false);
				$('#framediv').fadeIn();
				$('#widthspan').fadeOut();
				$('#width').prop('disabled', true);
				$('#width').fadeOut();
				$('#weightdiv input').prop('disabled', true);
				$('#weightdiv').fadeOut();
				options = categoryValue === "1" ? mediumOptions1 : mediumOptions2;
			}
			// if categoryId is 3, then all other non required fields are hidden (also disables 'required' keyword from input tag) and required fields are shown
			else if (categoryValue === "3") {
				$('#middiv label').html('Type');
				$('#medium').attr('name', 'type');
				$('#framediv input').prop('disabled', true);
				$('#framediv').fadeOut();
				$('#widthspan').fadeOut();
				$('#width').fadeOut();
				$('#width').prop('disabled', true);
				$('#weightdiv').fadeOut();
				$('#weightdiv').prop('disabled', true);
				options = typeOptions;
			} 
			// if categoryId is either 4 or 5, then all other non required fields are hidden (also disables 'required' keyword from input tag) and required fields are shown
			else if (categoryValue === "4" || categoryValue === "5") {
				$('#middiv label').html('Material');
				$('#medium').attr('name', 'material');
				$('#framediv').fadeOut();
				$('#framediv input').prop('disabled', true);
				$('#widthspan').fadeIn();
				$('#width').fadeIn();
				$('#width').prop('disabled', false);
				$('#weightdiv input').prop('disabled', false);
				$('#weightdiv').fadeIn();
				options = categoryValue === "4" ? materialOptions1 : materialOptions2;
			}
			// making the field empty after operation to re-insert required data
			$('#medium').empty();
			$('#medium').append('<option disabled value="">Choose</option>');

			<?php
			$selectedStr = '';
			if (isset($_GET['id'])) {
				// checking if item have medium or type or material
				if ($item['medium'] != null) {
					$selectedStr = $item['medium'];
				} else if ($item['type'] != null) {
					$selectedStr = $item['type'];
				} else if ($item['material'] != null) {
					$selectedStr = $item['material'];
				}
			}?>
			var isSelected = '<?= $selectedStr ?>'
			// showing required input fields by default when id is fetched
			$.each(options, function(index, option) {
				$('#medium').append($('<option>', {
					value: option.value,
					text: option.text,
				}));
			});
			$('#medium').val(isSelected);
		});

		<?php
		if (isset($_GET['id'])) {
			echo "$('#categories').trigger('change');";
		}
		?>
	});
</script>
</body>

</html>