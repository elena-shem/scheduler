/**
 * NOTE:
 *  Any element planned to be stored in an ArrMap instance, must implement the
 *  `toString` method to return an identifier-key that corresponds to the
 *  element. This return value will be used as key for the "Map" part.
 */
(function() {
    
    var ArrMap = function(array)
    {
        var i
        ;
        
        this.arr = [];
        this.length = 0;
        this.map = {};
        
        if(Array.isArray(array)) {
            for(i = 0; i < array.length; i++) {
                this.push(array[i]);
            }
        }
    };
    
    ArrMap.prototype.push = function(e)
    {
        if(this.map.hasOwnProperty(e)) {
            // no unique element
        } else {
            this.arr.push(e);
            this.length++;
            this.map[e] = e;
        }
        
        return this.length;
    };
    
    ArrMap.prototype.pop = function()
    {
        var e = this.arr.pop()
        ;
        
        if(typeof e !== "undefined") {
            this.length--;
            delete this.map[e];
        }
        
        return e;
    };
    
    ArrMap.prototype.indexOf = function(e)
    {
        return this.arr.indexOf(e);
    };
    
    ArrMap.prototype.at = function(index)
    {
        return this.arr[index];
    };
    
    ArrMap.prototype.remove = function(e)
    {
        if(this.map.hasOwnProperty(e)) {
            this.arr.splice(this.indexOf(e), 1);
            this.length--;
            delete this.map[e];
        }
        
        return this.length;
    };
    
    ArrMap.prototype.concat = function(arrmap)
    {
        return new ArrMap(this.arr.concat(arrmap.arr));
    };
    
    
    ArrMap.prototype.contains = function(e)
    {
        return this.map.hasOwnProperty(e);
    };
    
    
    ArrMap.prototype.get = function(e)
    {
        return this.map[e];
    };
    
    
    /* *************************************************************************
     * namespace definition
     * ************************************************************************/
    
    window.Scheduler = window.Scheduler || {};
    window.Scheduler.ArrMap = ArrMap;
    
})();