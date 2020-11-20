import Device from './Device';

export default class extends Device {
    constructor(options) {
        super(options);

        this.file = 'smartphone.glb'

        this.settings = {
            camera: {
                fov: 25,
                position: [0,0,10]
            },
            object: {
                rotation: [-12,-10,-18]
            }
        }

    }

    initGui() {
        super.initGui()

        let guiController = function() {
            this.rotation = {
                x: -12,
                y: -14,
                z: -18
            }
        };


        let guiInstance = new guiController();

        // PHONE
        let phone = this.gui.addFolder('Phone')
        let phoneRotation = phone.addFolder('Rotation')
        let rotationXController = phoneRotation.add(guiInstance.rotation, 'x', -180, 180);
        let rotationYController = phoneRotation.add(guiInstance.rotation, 'y', -180, 180);
        let rotationZController = phoneRotation.add(guiInstance.rotation, 'z', -180, 180);

        rotationXController.onChange((value) => {
            this.object.rotation.x = value * Math.PI / 180;
        });

        rotationYController.onChange((value) => {
            this.object.rotation.y = value * Math.PI / 180;
        });

        rotationZController.onChange((value) => {
            this.object.rotation.z = value * Math.PI / 180;
        });
        phone.open();
    }

    setupObject() {
        this.deviceMaterial = new THREE.MeshLambertMaterial({
            color: new THREE.Color(0xffffff),
            map: this.texture
        })

        this.reposition()

        this.object.rotation.x = this.settings.object.rotation[0] * Math.PI / 180;
        this.object.rotation.y = this.settings.object.rotation[1] * Math.PI / 180;
        this.object.rotation.z = this.settings.object.rotation[2] * Math.PI / 180;

        this.object.children[0].material = this.deviceMaterial
    }

    reposition() {
        if(this.object && this.object.position) {
            if(window.innerWidth > 700) {
                this.camera.position.z = 14.5
            } else {
                this.camera.position.z = 10.5
            }
        }
    }

    toggle(e) {
        super.toggle(e)
    }

    resize() {
        super.resize()

        this.reposition();
    }

    destroy() {
        super.destroy();

    }
}
