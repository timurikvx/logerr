

function useButtons(){

    document.removeEventListener('keydown', _keydown);
    document.addEventListener('keydown', _keydown);

    let code = null;
    let caller = null;
    let props = {}
    let f_buttons = {'F1': 112, 'F2': 113, 'F3': 114,'F4': 115, 'F5': 116, 'F6': 117, 'F7': 118, 'F8': 119, 'F9': 120, 'F10': 121, 'F11': 122, 'F12': 123};

    function escape(func, options){
        code = 27;
        caller = func;
        props = options || {};
    }

    function button(button, func, options){
        code = button.charCodeAt();
        caller = func;
        props = options || {};
    }

    function F(button, func, options){
        code = f_buttons[button];
        caller = func;
        props = options || {};
    }

    function shift(func, options){
        code = 16;
        caller = func;
        props = options || {};
    }

    function ctrl(func, options){
        code = 17;
        caller = func;
        props = options || {};
    }

    function enter(func, options){
        code = 13;
        caller = func;
        props = options || {};
    }

    function _keydown(e){
        if(code === e.keyCode){
            if(props.prevent){
                e.preventDefault();
            }
            if(props.stop){
                e.stopPropagation();
            }
            caller(e);
        }
    }

    return {
        escape: escape,
        button: button,
        shift: shift,
        ctrl: ctrl,
        enter: enter,
        F: F
    };

}

export {
    useButtons
}
