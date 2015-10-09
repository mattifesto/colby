"use strict";

var CBAdminImageThumbnailFactory = {

     createElement : function(args) {
         var element = document.createElement("div");
         element.className = "CBAdminImageThumbnail";
         var img;

         if (args.thumbnailURL !== "") {
             img = document.createElement("img");
             img.src = args.thumbnailURL;
         } else {
             img = document.createElement("div");
             img.textContent = "No thumbnail available";
         }

         element.appendChild(img);

         return element;
     }
};
