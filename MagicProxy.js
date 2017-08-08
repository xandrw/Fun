"use strict";

const _name = new WeakMap();

class User {
    constructor() {
        return Object.freeze({
            self: this,
            proxy: new Proxy(this, new MagicTrap())
        });
    }
    
    getName() {
        return _name.get(this);
    }
    
    setName(value) {
        _name.set(this, value);
    }
    
    foo() {
        return 'bar';
    }
}

class MagicTrap {
    get(target, property) {
        const normalizedProperty = _normalize('get', property);
        
        if (typeof target[normalizedProperty] === 'undefined')
            throw new Error(`Undefined property called: ${normalizedProperty}`);
        
        return target[normalizedProperty]();
    }
    
    set(target, property, value) {
        const normalizedProperty = _normalize('set', property);
        
        if (typeof target[normalizedProperty] === 'undefined')
            throw new Error(`Undefined property called: ${normalizedProperty}`);
        
        target[normalizedProperty](value);
        return true;
    }
}

class SimpleUser {
    constructor() {
        return Object.freeze({
            self: this,
            proxy: new Proxy(this, new SimpleMagictrap())
        });
    }

    foo() {
        return 'bar';
    }
}

const _privateMethods = {
    _getName: function(instance) {
        return _name.get(instance);
    },

    _setName: function(instance, value) {
        _name.set(instance, value);
    }
};

class SimpleMagictrap {
    get(target, property) {
        const normalizedProperty = _normalize('_get', property);

        if (typeof normalizedProperty === 'undefined')
            throw new Error(`Undefined property called: ${normalizedProperty}`);

        return _privateMethods[normalizedProperty](target);
    }
    
    set(target, property, value) {
        const normalizedProperty = _normalize('_set', property);

        if (typeof normalizedProperty === 'undefined')
            throw new Error(`Undefined property called: ${normalizedProperty}`);

        _privateMethods[normalizedProperty](target, value);
        return true;
    }
}

function _normalize(prefix, name) {
    // Uncomment below if you want to allow obj.getName call to be allowed
    // if (name.substring(0, prefix.length) === prefix) return name;
    const ucFirstName = name.charAt(0).toUpperCase() + name.slice(1);
    return `${prefix}${ucFirstName}`;
}

const user = new User();
user.proxy.name = 'Alex';
console.log(user.proxy.name);
console.log(user.self.constructor.name);
console.log(user.self instanceof User);
console.log(user.self.getName());

const simple = new SimpleUser();
simple.proxy.name = 'Ionut';
console.log(simple.proxy.name);
console.log(simple.self.constructor.name);
console.log(simple.self instanceof SimpleUser);
console.log(simple.self.foo());
