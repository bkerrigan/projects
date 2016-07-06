var images = [["default.jpg"],["barrels.jpg", "flowers.jpg", "glass.jpg", "grapes.jpg"], ["landscape.jpg", "leaves.jpg", "trike.jpg", "vines.jpg"]];
var current_set = 0;
var current_image = 0;
var timer;

function switchBackgroundSet () {
  var bg_select = document.getElementById("bg_select");
  var bg_set = bg_select.selectedIndex;
  this.current_set = bg_set;
  this.current_image = 0;
  this.changeBackground();
}

function changeBackground() {
  var bg_image = document.getElementById("bg_image");
  if (current_set < 0 || current_set >= images.length) {
    current_set = 0;
  }
  if (current_image >= images[current_set].length) {
    current_image = 0;
  }
  bg_image.src = "images/" + images[current_set][current_image];
  if (current_set != 0) {
    current_image++;
    clearInterval(timer);
    timer = setTimeout("changeBackground()", 5000);
  } else {
    clearInterval(timer);
  }
}
