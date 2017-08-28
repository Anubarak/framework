(function (root, factory) {
    if (typeof define === 'function' && define.amd) {
        define(['quill'], factory)
    } else if (typeof module !== 'undefined' && typeof exports === 'object') {
        module.exports = factory(require('quill'))
    } else {
        root.Requester = factory(root.Quill)
    }
}(this, function (Quill) {
    'use strict'

    var app;
    // declare ngQuill module
    app = angular.module('ngQuill', [])

    app.provider('ngQuillConfig', function () {
        var config = {
            modules: {
                toolbar: [
                    ['bold', 'italic', 'underline', 'strike'],        // toggled buttons
                    ['blockquote', 'code-block'],

                    [{ 'header': 1 }, { 'header': 2 }],               // custom button values
                    [{ 'list': 'ordered' }, { 'list': 'bullet' }],
                    [{ 'script': 'sub' }, { 'script': 'super' }],      // superscript/subscript
                    [{ 'indent': '-1' }, { 'indent': '+1' }],          // outdent/indent
                    [{ 'direction': 'rtl' }],                         // text direction

                    [{ 'size': ['small', false, 'large', 'huge'] }],  // custom dropdown
                    [{ 'header': [1, 2, 3, 4, 5, 6, false] }],

                    [{ 'color': [] }, { 'background': [] }],          // dropdown with defaults from theme
                    [{ 'font': [] }],
                    [{ 'align': [] }],

                    ['clean'],                                         // remove formatting button

                    ['link', 'image', 'video']                         // link and image, video
                ],
                imageResize: {
                    displaySize: true
                },
                imageDrop: true
            },
            theme: 'snow',
            placeholder: 'Insert text here ...',
            readOnly: false,
            bounds: document.body
        }

        this.set = function (customConf) {
            customConf = customConf || {}

            if (customConf.modules) {
                config.modules = customConf.modules
            }
            if (customConf.theme) {
                config.theme = customConf.theme
            }
            if (customConf.placeholder !== null && customConf.placeholder !== undefined) {
                config.placeholder = customConf.placeholder.trim()
            }
            if (customConf.bounds) {
                config.bounds = customConf.bounds
            }
            if (customConf.readOnly) {
                config.readOnly = customConf.readOnly
            }
            if (customConf.formats) {
                config.formats = customConf.formats
            }
        }

        this.$get = function () {
            return config
        }
    })

    app.component('ngQuillEditor', {
        bindings: {
            'modules': '<modules',
            'theme': '@?',
            'readOnly': '<?',
            'formats': '<?',
            'placeholder': '@?',
            'bounds': '<?',
            'onEditorCreated': '&?',
            'onContentChanged': '&?',
            'onSelectionChanged': '&?',
            'ngModel': '<',
            'maxLength': '<',
            'minLength': '<'
        },
        require: {
            ngModelCtrl: 'ngModel'
        },
        transclude: {
            'toolbar': '?ngQuillToolbar'
        },
        template: '<div class="ng-hide" ng-show="$ctrl.ready"><ng-transclude ng-transclude-slot="toolbar"></ng-transclude></div>',
        controller: ['$scope', '$element', '$timeout', '$transclude', 'ngQuillConfig', '$http', function ($scope, $element, $timeout, $transclude, ngQuillConfig, $http) {
            var config = {}
            var content
            var editorElem
            var modelChanged = false
            var editorChanged = false
            var editor
            var placeholder = ngQuillConfig.placeholder

            this.validate = function (text) {
                if (this.maxLength) {
                    if (text.length > this.maxLength + 1) {
                        this.ngModelCtrl.$setValidity('maxlength', false)
                    } else {
                        this.ngModelCtrl.$setValidity('maxlength', true)
                    }
                }

                if (this.minLength > 1) {
                    // validate only if text.length > 1
                    if (text.length <= this.minLength && text.length > 1) {
                        this.ngModelCtrl.$setValidity('minlength', false)
                    } else {
                        this.ngModelCtrl.$setValidity('minlength', true)
                    }
                }
            }

            this.$onChanges = function (changes) {
                if (changes.ngModel && changes.ngModel.currentValue !== changes.ngModel.previousValue) {
                    content = changes.ngModel.currentValue

                    if (editor && !editorChanged) {
                        modelChanged = true
                        if (content) {
                            editor.setContents(editor.clipboard.convert(content))
                        } else {
                            editor.setText('')
                        }
                    }
                    editorChanged = false
                }

                if (editor && changes.readOnly) {
                    editor.enable(!changes.readOnly.currentValue)
                }
            }

            this.$onInit = function () {
                if (this.placeholder !== null && this.placeholder !== undefined) {
                    placeholder = this.placeholder.trim()
                }

                config = {
                    theme: this.theme || ngQuillConfig.theme,
                    readOnly: this.readOnly || ngQuillConfig.readOnly,
                    modules: this.modules || ngQuillConfig.modules,
                    formats: this.formats || ngQuillConfig.formats,
                    placeholder: placeholder,
                    bounds: this.bounds || ngQuillConfig.bounds
                }
            }

            this.$postLink = function () {
                // create quill instance after dom is rendered
                $timeout(function () {
                    this._initEditor(editorElem)
                }.bind(this), 0)
            }

            this._initEditor = function (editorElem) {
                var $editorElem = angular.element('<div></div>')
                var container = $element.children()

                editorElem = $editorElem[0]

                // set toolbar to custom one
                if ($transclude.isSlotFilled('toolbar')) {
                    config.modules.toolbar = container.find('ng-quill-toolbar').children()[0]
                }

                container.append($editorElem)

                editor = new Quill(editorElem, config)



                var selectLocalImage = function() {
                    const input = document.createElement('input');
                    input.setAttribute('type', 'file');
                    input.click();

                    // Listen upload local image and save to server
                    input.onchange = function(){
                        const file = input.files[0];

                        // file type is only image.
                        if (/^image\//.test(file.type)) {
                            console.log(file);
                            saveToServer(file);

                        } else {
                            console.warn('You could only upload images.');
                        }
                    };
                };

                /**
                 * Step2. save to server
                 *
                 * @param file
                 */
                var saveToServer = function(file) {
                    const fd = new FormData();
                    fd.append('image', file);
                    fd.append('action', "asset/storeAsset");


                    $http({
                        method: 'POST',
                        url: '',
                        headers: { 'Content-Type': undefined },
                        transformRequest: angular.identity,
                        data: fd
                    }).then(function successCallback(response) {
                        console.log(response.data.url);
                        console.log(response);
                        insertToEditor(response.data.url);
                        // this callback will be called asynchronously
                        // when the response is available
                    }, function errorCallback(response) {
                        console.log(response);
                        // called asynchronously if an error occurs
                        // or server returns response with an error status.
                    });

                    /**
                    const xhr = new XMLHttpRequest();
                    xhr.open('POST', 'http://localhost:3000/upload/image', true);
                    xhr.onload = function(){
                        if (xhr.status === 200) {
                            // this is callback data: url
                            const url = JSON.parse(xhr.responseText).data;
                            insertToEditor(url);
                        }
                    };*/
                    //xhr.send(fd);
                };

                /**
                 * Step3. insert image url to rich editor.
                 *
                 * @param url
                 */
                var insertToEditor = function(string) {
                    // push image url to rich editor.
                    const range = editor.getSelection();
                    editor.insertEmbed(range.index, 'image', string);
                };


                // quill editor add image handler
                editor.getModule('toolbar').addHandler('image', function(){
                    selectLocalImage()
                });

                this.ready = true

                // mark model as touched if editor lost focus
                editor.on('selection-change', function (range, oldRange, source) {
                    if (this.onSelectionChanged) {
                        this.onSelectionChanged({
                            editor: editor,
                            oldRange: oldRange,
                            range: range,
                            source: source
                        })
                    }

                    if (range) {
                        return
                    }
                    $scope.$applyAsync(function () {
                        this.ngModelCtrl.$setTouched()
                    }.bind(this))
                }.bind(this))

                // update model if text changes
                editor.on('text-change', function (delta, oldDelta, source) {
                    var html = editorElem.children[0].innerHTML
                    var text = editor.getText()

                    if (html === '<p><br></p>') {
                        html = null
                    }
                    this.validate(text)

                    if (!modelChanged) {
                        $scope.$applyAsync(function () {
                            editorChanged = true

                            this.ngModelCtrl.$setViewValue(html)

                            if (this.onContentChanged) {
                                this.onContentChanged({
                                    editor: editor,
                                    html: html,
                                    text: text,
                                    delta: delta,
                                    oldDelta: oldDelta,
                                    source: source
                                })
                            }
                        }.bind(this))
                    }
                    modelChanged = false
                }.bind(this))

                // set initial content
                if (content) {
                    modelChanged = true

                    var contents = editor.clipboard.convert(content)
                    editor.setContents(contents)
                    editor.history.clear()
                }

                // provide event to get informed when editor is created -> pass editor object.
                if (this.onEditorCreated) {
                    this.onEditorCreated({editor: editor})
                }
            }
        }]
    })

    return app.name
}))
