//Vue Filters

//Uppercase's the first letter
import moment from "../app";

Vue.filter('upText', function (value) {
  return value.charAt(0).toUpperCase() + value.slice(1);
})

