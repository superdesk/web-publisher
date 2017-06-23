
function hsb2rgb(hue, saturation, value) {
  hue = (parseInt(hue, 10) || 0) % 360;

  saturation = /%/.test(saturation)
    ? parseInt(saturation, 10) / 100
    : parseFloat(saturation, 10);

  value = /%/.test(value)
    ? parseInt(value, 10) / 100
    : parseFloat(value, 10);

  saturation = Math.max(0, Math.min(saturation, 1));
  value = Math.max(0, Math.min(value, 1));

  var rgb;
  if (saturation === 0) {
    return [
      Math.round(255 * value),
      Math.round(255 * value),
      Math.round(255 * value)
    ];
  }

  var side = hue / 60;
  var chroma = value * saturation;
  var x = chroma * (1 - Math.abs(side % 2 - 1));
  var match = value - chroma;

  switch (Math.floor(side)) {
  case 0: rgb = [ chroma, x, 0 ]; break;
  case 1: rgb = [ x, chroma, 0 ]; break;
  case 2: rgb = [ 0, chroma, x ]; break;
  case 3: rgb = [ 0, x, chroma ]; break;
  case 4: rgb = [ x, 0, chroma ]; break;
  case 5: rgb = [ chroma, 0, x ]; break;
  default: rgb = [ 0, 0, 0 ];
  }

  rgb[0] = Math.round(255 * (rgb[0] + match));
  rgb[1] = Math.round(255 * (rgb[1] + match));
  rgb[2] = Math.round(255 * (rgb[2] + match));

  return rgb;
}


module.exports = hsb2rgb;
