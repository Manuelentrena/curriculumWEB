"use strict";

const boton_foto = document.querySelector("#avatar");
const imagen = document.querySelector("#user-photo");
const boton_guardar = document.querySelector("#btn-guardar");
const url_img = document.querySelector("#url_avatar");

var widget_cloudinary = cloudinary.createUploadWidget(
  {
    cloudName: "manuelentrena",
    uploadPreset: "curriculumweb",
  },
  (error, result) => {
    if (!error && result && result.event === "success") {
      console.log("imagen subida con éxito", result.info);
      imagen.src = result.info.secure_url;
      url_img.value = result.info.secure_url;
    }
  }
);

boton_foto.addEventListener(
  "click",
  () => {
    widget_cloudinary.open();
  },
  false
);
