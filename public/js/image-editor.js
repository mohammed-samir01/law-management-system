(function () {
    'use strict';

    function registerImageEditor() {
        if (typeof Alpine === 'undefined' || typeof Alpine.data === 'undefined') return;

        Alpine.data('imageEditor', function () {
            return {
                open: false,
                imageUrl: '',
                originalPath: '',
                cropper: null,
                loading: false,
                saved: false,
                mode: 'crop',

                brightness: 100,
                contrast: 100,
                saturation: 100,

                activeFilter: 'none',
                selectedAspect: 'free',

                filters: {
                    none:     { label: 'أصلي',       css: '' },
                    bw:       { label: 'أبيض وأسود',  css: 'grayscale(100%)' },
                    sepia:    { label: 'كلاسيكي',    css: 'sepia(80%)' },
                    warm:     { label: 'دافئ',        css: 'saturate(140%) hue-rotate(-15deg) brightness(108%)' },
                    cool:     { label: 'بارد',        css: 'saturate(75%) hue-rotate(25deg) brightness(95%)' },
                    vivid:    { label: 'نابض',        css: 'saturate(180%) contrast(115%) brightness(102%)' },
                    fade:     { label: 'باهت',        css: 'brightness(115%) contrast(80%) saturate(65%)' },
                    dramatic: { label: 'درامي',       css: 'contrast(140%) brightness(88%) saturate(120%)' },
                },

                aspects: {
                    free:  { label: 'حر',    value: NaN },
                    '1:1': { label: '1:1',   value: 1 },
                    '4:3': { label: '4:3',   value: 4/3 },
                    '3:2': { label: '3:2',   value: 3/2 },
                    '16:9':{ label: '16:9',  value: 16/9 },
                    '2:3': { label: '2:3',   value: 2/3 },
                    '3:4': { label: '3:4',   value: 3/4 },
                },

                openEditor: function (imageUrl, originalPath) {
                    this.imageUrl     = imageUrl;
                    this.originalPath = originalPath;
                    this.open         = true;
                    this.saved        = false;
                    this.resetAll();
                    var self = this;
                    this.$nextTick(function () { self.initCropper(); });
                },

                initCropper: function () {
                    if (typeof Cropper === 'undefined') { console.error('Cropper.js not loaded'); return; }
                    var img = this.$refs.cropImg;
                    if (!img) return;
                    if (this.cropper) { this.cropper.destroy(); }
                    var self = this;
                    this.cropper = new Cropper(img, {
                        aspectRatio: NaN,
                        viewMode: 1,
                        dragMode: 'crop',
                        autoCropArea: 0.85,
                        responsive: true,
                        guides: true,
                        center: true,
                        cropBoxMovable: true,
                        cropBoxResizable: true,
                        toggleDragModeOnDblclick: true,
                        ready: function () { self.applyVisualFilter(); },
                    });
                },

                resetAll: function () {
                    this.mode           = 'crop';
                    this.brightness     = 100;
                    this.contrast       = 100;
                    this.saturation     = 100;
                    this.activeFilter   = 'none';
                    this.selectedAspect = 'free';
                },

                setAspect: function (key) {
                    this.selectedAspect = key;
                    if (this.cropper) this.cropper.setAspectRatio(this.aspects[key].value);
                },

                rotateLeft:  function () { if (this.cropper) this.cropper.rotate(-90); },
                rotateRight: function () { if (this.cropper) this.cropper.rotate(90); },
                flipH: function () {
                    if (!this.cropper) return;
                    var d = this.cropper.getData();
                    this.cropper.scaleX((d.scaleX === -1) ? 1 : -1);
                },
                flipV: function () {
                    if (!this.cropper) return;
                    var d = this.cropper.getData();
                    this.cropper.scaleY((d.scaleY === -1) ? 1 : -1);
                },
                zoomIn:    function () { if (this.cropper) this.cropper.zoom(0.15); },
                zoomOut:   function () { if (this.cropper) this.cropper.zoom(-0.15); },
                resetCrop: function () {
                    if (this.cropper) { this.cropper.destroy(); this.cropper = null; }
                    this.resetAll();
                    var self = this;
                    this.$nextTick(function () { self.initCropper(); });
                },

                setFilter: function (key) {
                    this.activeFilter = key;
                    if (key !== 'none') {
                        this.brightness = 100;
                        this.contrast   = 100;
                        this.saturation = 100;
                    }
                    this.applyVisualFilter();
                },

                applyVisualFilter: function () {
                    var img = this.$refs.cropImg;
                    if (img) img.style.filter = this.getFilterCSS();
                },

                getFilterCSS: function () {
                    if (this.activeFilter !== 'none') {
                        return this.filters[this.activeFilter].css;
                    }
                    return 'brightness(' + this.brightness + '%) contrast(' + this.contrast + '%) saturate(' + this.saturation + '%)';
                },

                saveImage: function () {
                    if (!this.cropper || this.loading) return;
                    this.loading = true;
                    var self = this;

                    var canvas = this.cropper.getCroppedCanvas({
                        maxWidth: 2400,
                        maxHeight: 2400,
                        imageSmoothingEnabled: true,
                        imageSmoothingQuality: 'high',
                    });

                    var final  = document.createElement('canvas');
                    final.width  = canvas.width;
                    final.height = canvas.height;
                    var ctx = final.getContext('2d');
                    ctx.filter = this.getFilterCSS();
                    ctx.drawImage(canvas, 0, 0);

                    final.toBlob(function (blob) {
                        var fd = new FormData();
                        fd.append('image', blob, 'edited.webp');
                        fd.append('original_path', self.originalPath);

                        var csrfMeta = document.querySelector('meta[name="csrf-token"]');
                        var token = csrfMeta ? csrfMeta.getAttribute('content') : '';

                        fetch('/admin/image-editor/save', {
                            method: 'POST',
                            headers: { 'X-CSRF-TOKEN': token },
                            body: fd,
                        })
                        .then(function (res) { return res.json(); })
                        .then(function (data) {
                            if (data.success) {
                                self.saved = true;
                                setTimeout(function () { self.close(); }, 800);
                            } else {
                                alert('فشل الحفظ: ' + (data.message || ''));
                            }
                        })
                        .catch(function (e) { alert('خطأ: ' + e.message); })
                        .finally(function () { self.loading = false; });
                    }, 'image/webp', 0.88);
                },

                close: function () {
                    this.open = false;
                    if (this.cropper) { this.cropper.destroy(); this.cropper = null; }
                },
            };
        });
    }

    // Works whether Alpine has initialized or not
    if (typeof Alpine !== 'undefined' && Alpine.data) {
        registerImageEditor();
    } else {
        document.addEventListener('alpine:init', registerImageEditor);
    }
})();
