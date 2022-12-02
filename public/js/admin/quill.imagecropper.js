document.addEventListener('orchid:quill', (event) => {
    // Object for registering plugins
    event.detail.quill.register("modules/imageCompressor", imageCompressor);
    console.log(event.detail.options.modules);
    let toolbar = [
        [{ 'header': [1, 2, 3, false] }],
        ['bold', 'italic', 'underline', 'blockquote'],
        [{ 'align': [] }],        // toggled buttons

        [{ 'header': 1 }, { 'header': 2 }],        // custom button values
        [{ 'list': 'ordered'}, { 'list': 'bullet' }],
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
            maxHeight: 1000, // default
            imageType: 'image/jpeg'
        }
    };
});
