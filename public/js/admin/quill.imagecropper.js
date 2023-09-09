document.addEventListener('orchid:quill', (event) => {
    event.detail.quill.register("modules/imageCompressor", imageCompressor);

    let toolbar = [
      [{'header': [1, 2, 3, false]}],
      ['bold', 'italic', 'underline', 'blockquote'],
      [{'align': []}],        // toggled buttons
      [{'list': 'ordered'}, {'list': 'bullet'}],
      [
        "image",
        "video"
      ],
      ['clean']
    ];
    // Parameter object for initialization
    event.detail.options.modules = {
      toolbar: toolbar,
      imageCompressor: {
        quality: 0.9,
        maxWidth: 1000, // default
        maxHeight: 700, // default
        imageType: 'image/jpeg'
      },
    };

  });
  // Object for registering plugins
