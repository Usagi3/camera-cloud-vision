(function (){
    function CameraManager () {
        this.video = document.createElement('video');
        this.video.setAttribute("playsinline", true);
        this.videoViewer = document.querySelector('.video-viewer');
        this.canvas = document.querySelector('#canvas-video');
        this.playBtn = document.querySelector('#play-btn');
        this.pauseBtn = document.querySelector('#pause-btn');
        this.resumeBtn = document.querySelector('#resume-btn');
        this.snapshotBtn = document.querySelector('#snapshot-btn');
        this.ocrArea = document.querySelector('.ocr');
        // this.ocrProgress = document.querySelector('#progress > span');
        this.ocrTextarea = document.querySelector('#textarea');
        this.loader = new ViewLoader(document.querySelector('.loader-area'));
    }
    CameraManager.prototype = {
        setEventHandler: function () {
            this.playBtn.addEventListener('click', function () {
                this.play();
            }.bind(this));
            this.pauseBtn.addEventListener('click', function () {
                this.pause();
            }.bind(this));
            this.resumeBtn.addEventListener('click', function () {
                this.resume();
            }.bind(this));
            this.snapshotBtn.addEventListener('click', function () {
                this.snapshot();
            }.bind(this));
        },
        // ocr: function (canvas) {
        //     const progress = this.ocrProgress;
        //     const textarea = this.ocrTextarea;
        //     Tesseract
        //         .recognize(
        //             canvas,
        //             'eng+jpn',
        //             {
        //                 logger: m => {
        //                     console.log(m);
        //                     if (m.status !== 'recognizing text') return;
        //                     progress.innerText = Math.floor(m.progress * 10000) / 100;
        //                 }
        //             }
        //         ).then(({ data: { text } }) => {
        //         console.log(text);
        //         textarea.value = text;
        //     });
        // },
        ocrReset: function () {
            // this.ocrProgress.innerText = 0;
            this.ocrTextarea.value = '';
        },
        play: function () {
            navigator.mediaDevices.getUserMedia({
                video: {
                    width: 1920,
                    height: 1040,
                    // facingMode: { exact: "environment" }
                    facingMode: "user"
                }
            }).then(function(stream) {
                this.viewBtnMap('play');
                this.startStream(stream);
            }.bind(this)).catch(function (err) {
                console.log(err.code);
                if (err === 'NotAllowedError') {
                    alert('カメラの起動が拒否されました');
                } else {
                    alert('カメラを使用することができません');
                }
            });
        },
        pause: function () {
            this.cropper = new Cropper(
                this.canvas
                // ,{aspectRatio: 16 / 9}
            );
            this.viewBtnMap('pause');
            this.streaming = false;
        },
        resume: function () {
            this.ocrReset();
            this.cropper.destroy();
            this.viewBtnMap('resume');
            this.streaming = true;
        },
        snapshot: function () {
            // this.ocrReset();
            this.viewBtnMap('snapshot');
            this.loader.show();
            this.cropper.getCroppedCanvas().toBlob(function (blob) {
                let formData = new FormData();
                formData.append('img', blob, 'blob.png');
                fetch('./', {
                    method: 'POST',
                    headers: {
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    body: formData
                }).then(response => response.json()).then(data => {
                    this.ocrTextarea.value = data.data.join(' ');
                    this.loader.hide();
                }).catch(error => {
                    console.log(error);
                });
            }.bind(this));
            // this.ocr(this.cropper.getCroppedCanvas());
        },
        startStream: function (stream) {
            this.streaming = true;
            this.video.srcObject = stream;
            this.video.play();
            this.stream();
        },
        stream: function () {
            requestAnimationFrame(function () {
                if (this.streaming) {
                    if (this.video.readyState === this.video.HAVE_ENOUGH_DATA) {
                        this.changeView([this.videoViewer], true);
                        this.setCanvas(this.canvas, this.video);
                        this.changeView([this.ocrArea], true);
                    }
                }
                this.stream();
            }.bind(this));
        },
        setCanvas: function (canvas, video) {
            document.querySelector('.debug').textContent = 'height: '+video.videoHeight+', width: '+video.videoWidth;
            const height = video.videoHeight;
            canvas.height = height;
            canvas.width = video.videoWidth;
            const ctx = canvas.getContext("2d");
            ctx.drawImage(video, 0, 0, video.videoWidth, height, 0, 0, video.videoWidth, height);
        },
        viewBtnMap: function (type) {
            if (type === 'play') {
                this.changeView([this.playBtn], false);
                this.changeView([this.pauseBtn], true);
            } else if (type === 'pause') {
                this.changeView([this.pauseBtn], false);
                this.changeView([this.resumeBtn, this.snapshotBtn], true);
            } else if (type === 'resume') {
                this.changeView([this.resumeBtn, this.snapshotBtn], false);
                this.changeView([this.pauseBtn], true);
            } else if (type === 'snapshot') {
                // this.changeView([this.snapshotBtn], false);
            }
        },
        changeView: function (elements, isShow) {
            const className = 'hide';
            elements.forEach(elem => {
                if (isShow) {
                    elem.classList.remove(className);
                } else {
                    elem.classList.add(className);
                }
            });
        }
    }
    function ViewLoader(loaderArea) {
        this.hideClassName = 'hide';
        this.loaderArea = loaderArea;
    }
    ViewLoader.prototype = {
        show: function() {
            this.loaderArea.classList.remove(this.hideClassName);
        },
        hide: function() {
            setTimeout(function (){
                this.loaderArea.classList.add(this.hideClassName);
            }.bind(this), 500);
        }
    }
    document.addEventListener('DOMContentLoaded', function() {
        const cameraManager = new CameraManager();
        cameraManager.setEventHandler();
    });
})();