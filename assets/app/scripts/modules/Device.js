import { APP_STATE } from '../state';
import { module } from 'modujs';
import { lerp } from '../utils/maths'

const FILETYPES = {
    FBX: 'fbx',
    OBJ: 'obj',
    GLB: 'glb',
    GLTF: 'gltf'
}

export default class {
    constructor(el) {

        this.el = el;

        this.showGui = this.el.getAttribute('data-gui');

        this.rotationAmplitudes = {
            x: 35,
            y: 2
        }

        this.settings = {
            camera: {
                fov: 25,
                position: [0,0,10]
            }
        }

        this.device = this.el.getAttribute('data-device');
        this.deviceColor = this.el.getAttribute('data-color') && this.el.getAttribute('data-color').length ? this.el.getAttribute('data-color') : 'ffffff'

        this.autoshow = this.el.getAttribute('data-autoshow') && this.el.getAttribute('data-autoshow').length ? this.el.getAttribute('data-autoshow') : true
    }

    init() {


        // Set events and such
        this.initScene();
        this.initLights();
        let initPromise = this.initObject()
        this.initScrollTl()

        if(this.showGui) {
            initPromise.then(() => {
                this.initGui()
            })
        }

        Promise.all([initPromise]).then(() => {
            if(!this.autoshow || window.isMobile) {
                setTimeout(() => {
                    this.slideUp();
                }, 1600);
            }
        });

        this.checkResizeBind = this.checkResize.bind(this)
        window.addEventListener('resize', this.checkResizeBind);
        this.checkResize()

        this.manageInteractions()
    }

    checkResize() {
        if(window.isMobile) {
            this.resize();
        } else {
            if(!this.resizeTick) {
                requestAnimationFrame(() => {
                    this.resize();
                });
                this.resizeTick = true;
            }
        }
    }

    initScene() {

        this.scene,
        this.camera,
        this.renderer,
        this.element;

        this.scene = new THREE.Scene();

        // CAMERA
        // ==========================================================================
        this.camera = new THREE.PerspectiveCamera(this.settings.camera.fov, window.innerWidth / window.innerHeight, 1, 20000);
        // this.camera = new THREE.OrthographicCamera( window.innerWidth / - 2, window.innerWidth / 2, window.innerHeight / 2, window.innerHeight / - 2, 1, 1000000 );
        this.camera.position.set(this.settings.camera.position[0], this.settings.camera.position[1], this.settings.camera.position[2]);
        this.camera.lookAt(0,0,0);
        this.scene.add(this.camera);

        // MOUSE
        // ==========================================================================
        this.mouse = new THREE.Vector2();
        this.mouse.x = - window.innerWidth / 2;
        this.mouse.y = - window.innerHeight / 2;

        // RENDERER
        // ==========================================================================
        this.renderer = new THREE.WebGLRenderer({ antialias: (window.devicePixelRatio >= 2 ) ? false : true, alpha:true});
        this.renderer.setPixelRatio(Math.min(window.devicePixelRatio,1.5));
        // this.renderer.shadowMap.enabled = true;
        // this.renderer.shadowMap.type = THREE.PCFSoftShadowMap;
        // this.renderer.shadowMap.needsUpdate = true
        this.element = this.renderer.domElement;
        this.container = this.el;
        this.container.width = this.el.getBoundingClientRect().width;
        this.container.height = this.el.getBoundingClientRect().height;

        this.element.width = this.container.width;
        this.element.height = this.container.height;

        this.renderer.setSize( this.container.width, this.container.height );
        // this.renderer.shadowMap.enabled = true;
        this.renderer.gammaInput = true;
        this.renderer.gammaOutput = true;
        // this.renderer.setPixelRatio(1);

        this.container.appendChild(this.element);
    }

    initLights() {
        // SPOTLIGHT
        // ==========================================================================
        this.spotLight = new THREE.DirectionalLight( 0xcccccc, .5);
        this.spotLight.position.set(-500,500,200);
        this.spotLight.castShadow = true;

        this.camera.add( this.spotLight );

        // AMBIANT LIGHT
        // ==========================================================================

        this.ambientLight = new THREE.AmbientLight( 0xffffff, .75 ); // soft white light
        this.scene.add( this.ambientLight );

        // this.scene.add( new THREE.HemisphereLight( 0x443333, 0x111122 ) );
    }

    initObject() {
        let count = 1;

        this.scrollWrapper = new THREE.Object3D();
        this.slideUpWrapper = new THREE.Object3D();
        this.wrapper = new THREE.Object3D();

        this.wrapper.rotationTarget = {
            x: 0,
            y: 0,
            z: 0
        }

        this.texture = new THREE.TextureLoader().load(this.textureSrc);

        return this.loadModel(this.file).then(object => {
            this.object = object;

            this.setupObject()

            this.scrollWrapper.add(this.slideUpWrapper)
            this.slideUpWrapper.add(this.wrapper)
            this.wrapper.add(this.object)
            this.scene.add(this.scrollWrapper)

            if(this.autoshow) this.slideUpWrapper.position.y = -200
        });
    }

    setupObject() { }

    initScrollTl() {
        this.scrollTl = new TimelineMax({})

        // Object Rotation
        this.scrollTl.fromTo(this.scrollWrapper.rotation, 1, { y: 5  * Math.PI / 180 }, { y: -15 * Math.PI / 180, ease: "none" }, 0)

        this.scrollTl.pause()
    }

    // Animations
    // ==========================================================================

    slideUp(param) {
        const DURATION = 1

        this.slideUpTl = new TimelineMax({
            onComplete: () => {
                if(param !== undefined) {
                    param.callback()
                }
            }
        })
        this.slideUpTl.addLabel('start', 0)
        this.slideUpTl.fromTo(this.slideUpWrapper.position, DURATION, { y: -10 }, { y: 0, ease: Power3.easeOut }, 'start')
        this.slideUpTl.fromTo(this.slideUpWrapper.rotation, DURATION, { y: 90 * Math.PI / 180 }, { y: 0, ease: Power3.easeOut }, 'start')
    }

    slideOut(param) {
        const DURATION = 1

        this.slideOutTl = new TimelineMax({
            onComplete: () => {
                if(param && param.callback) param.callback()
            }
        })
        this.slideOutTl.addLabel('start', 0)
        this.slideOutTl.fromTo(this.slideUpWrapper.position, DURATION, { y: 0 }, { y: 10, ease: Power2.easeIn }, 'start')
        this.slideOutTl.to(this.slideUpWrapper.rotation, DURATION, { y: 90 * Math.PI / 180, ease: Power2.easeIn }, 'start')
    }

    // ==========================================================================
    // GUI
    // ==========================================================================
    initGui() {
        this.gui = new dat.GUI();

        // CAMERA
        let camera = this.gui.addFolder('Camera');
        camera.add(this.camera, 'fov', 10, 500)
        camera.add(this.camera.position, 'x', 0, 20)
        camera.add(this.camera.position, 'y', 0, 20)
        camera.add(this.camera.position, 'z', 0, 20)
        camera.open();

        // AMBIENT LIGHT
        let ambientLight = this.gui.addFolder('Ambient Light');
        ambientLight.add(this.ambientLight, 'intensity', 0.0, 1.0)
        ambientLight.open();

        // SPOT LIGHT
        let spotLight = this.gui.addFolder('SpotLight');
        spotLight.add(this.spotLight, 'intensity', 0.0, 1.0)
        spotLight.open();
    }

    // ==========================================================================
    // UTILS
    // ==========================================================================
    loadModel(filename) {
        const PATH = '/assets/app/3d/models/'
        const FILETYPE = filename.substr(filename.lastIndexOf('.')+1).toLowerCase()

        if(APP_STATE.models3d[PATH+filename]) {
            if(FILETYPE == FILETYPES.GLB || FILETYPE == FILETYPES.GLTF)
                this.texture.flipY = false

            return new Promise(resolve => { resolve() }).then(() => { return APP_STATE.models3d[PATH+filename].clone() })
        }

        let promise;
        switch(FILETYPE) {
            case FILETYPES.FBX:
                if(!this.fbxLoader)
                    this.fbxLoader = new THREE.FBXLoader();

                promise = new Promise((resolve) => { this.fbxLoader.load(PATH+filename, resolve)})
                break;
            case FILETYPES.OBJ:
                if(!this.objLoader)
                    this.objLoader = new THREE.OBJLoader();

                promise = new Promise((resolve) => { this.objLoader.load(PATH+filename, resolve)})
                break;
            case FILETYPES.GLB:
            case FILETYPES.GLTF:
                if(!this.gltfLoader)
                    this.gltfLoader = new THREE.GLTFLoader();

                this.texture.flipY = false

                promise = new Promise((resolve) => { this.gltfLoader.load(PATH+filename, resolve) }).then(object => object.scene)
                break;
            default:
                return false
                break;
        }

        return promise.then(object => {
            APP_STATE.models3d[PATH+filename] = object

            return object.clone()
        })
    }


    // ==========================================================================
    // INTERACTIONS
    // ==========================================================================

    manageInteractions() {
        this.mouse = new THREE.Vector2()

        this.mouseMoveBind = (e) => {
            // console.log(e.clientX, e.clientY);

            this.mouse.x = (e.clientX - window.innerWidth/2) / (window.innerWidth) * this.rotationAmplitudes.x
            this.mouse.y = (e.clientY - window.innerHeight/2) / (window.innerHeight) * this.rotationAmplitudes.y

            // console.log(this.mouse);

            this.wrapper.rotationTarget.x = (-this.mouse.y * Math.PI / 180) * 2
            this.wrapper.rotationTarget.y = (-this.mouse.x * Math.PI / 180)
        }

        window.addEventListener('mousemove', this.mouseMoveBind);

        // DEVICE ORIENTATION
        // this.orientation       = {};
        // this.orientation.alpha = 0;
        // this.orientation.gamma = 0;
        // this.orientation.beta  = 0;

        // this.relativeOrientation       = {};
        // this.relativeOrientation.alpha = 0;
        // this.relativeOrientation.gamma = 0;
        // this.relativeOrientation.beta  = 0;

        // $window.on(EVENT.ORIENTATION, (e) => {
        //     e = e.originalEvent;

        //     // console.log(e);

        //     let diff   = {};
        //     diff.alpha = e.alpha - this.orientation.alpha;
        //     diff.gamma = e.gamma - this.orientation.gamma;
        //     diff.beta  = e.beta - this.orientation.beta;

        //     this.relativeOrientation.alpha = Math.max(-180, Math.min(180, this.relativeOrientation.alpha + diff.alpha));
        //     this.relativeOrientation.gamma = Math.max(-90, Math.min(90, this.relativeOrientation.gamma + diff.gamma));
        //     this.relativeOrientation.beta  = Math.max(-90, Math.min(90, this.relativeOrientation.beta + diff.beta));

        //     // console.log(this.relativeOrientation);

        //     this.wrapper.rotationTarget.x = this.relativeOrientation.beta * Math.PI / 180
        //     this.wrapper.rotationTarget.y = this.relativeOrientation.gamma * Math.PI / 180


        //     this.orientation.alpha = e.alpha;
        //     this.orientation.gamma = e.gamma;
        //     this.orientation.beta  = e.beta;
        // })

        this.onHeaderProgressBind = (e) => {
            this.scrollTl.progress(e.options.progress)
        }
    }

    // ==========================================================================
    // LOOP
    // ==========================================================================
    animate(){
        this.raf = requestAnimationFrame(()=>this.animate());
        this.render();
    }

    stop() {
        if(this.raf) cancelAnimationFrame(this.raf);
    }

    toggle(e) {
        if (e.way === "enter") {
            this.animate()
        } else {
            this.stop()
        }
    }

    render(){

        if(this.wrapper !== undefined) {
            this.wrapper.rotation.x = lerp(this.wrapper.rotation.x, this.wrapper.rotationTarget.x, 0.1);
            this.wrapper.rotation.y = lerp(this.wrapper.rotation.y, this.wrapper.rotationTarget.y, 0.1);
            this.wrapper.rotation.z = lerp(this.wrapper.rotation.z, this.wrapper.rotationTarget.z, 0.1);
        }
        if(this.camera !== undefined) {
            this.camera.updateProjectionMatrix();
        }

        if(this.renderer !== undefined) {
            this.renderer.render(this.scene, this.camera);
        }
    }

    resize() {
        this.container.width = this.el.getBoundingClientRect().width;
        this.container.height = this.el.getBoundingClientRect().height;

        this.element.width = this.container.width;
        this.element.height = this.container.height;
        this.camera.aspect = this.container.width / this.container.height;
        this.renderer.setSize(this.container.width, this.container.height);

        this.resizeTick = false;
    }

    destroy() {
        super.destroy();
        if(this.raf) cancelAnimationFrame(this.raf);

        window.removeEventListener('resize', this.mouseMoveBind);
        window.removeEventListener('mousemove', this.mouseMoveBind);
    }
}
