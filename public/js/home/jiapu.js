window.onload = function(){
      var card_id = null;
      if (window.goSamples) goSamples();  // init for these samples -- you don't need to call this
      var $ = go.GraphObject.make;
      myDiagram =
        $(go.Diagram, "myDiagramDiv",
          {
            initialAutoScale: go.Diagram.Uniform,
            initialContentAlignment: go.Spot.Center,
            allowDragOut:true,
            "undoManager.isEnabled": true,
            // when a node is selected, draw a big yellow circle behind it
            nodeSelectionAdornmentTemplate:
              $(go.Adornment, "Auto",
                { layerName: "Grid" },  // the predefined layer that is behind everything else
                // $(go.Shape, "Circle", { fill: "yellow", stroke: null }),
                $(go.Placeholder)
              ),
            layout:  // use a custom layout, defined below
              $(GenogramLayout, { direction: 90, layerSpacing: 30, columnSpacing: 10 })
          });


    cardDiagram =
    $(go.Diagram, "card",
      {
        // initialAutoScale: go.Diagram.Uniform,
        initialContentAlignment: go.Spot.Center,
        "undoManager.isEnabled": true,
        // when a node is selected, draw a big yellow circle behind it
        nodeSelectionAdornmentTemplate:
          $(go.Adornment, "Auto",
            { layerName: "Grid" },  // the predefined layer that is behind everything else
            // $(go.Shape, "Circle", { fill: "yellow", stroke: null }),
            $(go.Placeholder)
          ),
        layout:  // use a custom layout, defined below
          $(GenogramLayout, { direction: 90, layerSpacing: 30, columnSpacing: 10 })
      });


    cardDiagram.model =
    go.GraphObject.make(go.GraphLinksModel,
    { // declare support for link label nodes
        linkLabelKeysProperty: "labelKeys",
        // this property determines which template is used
        nodeCategoryProperty: "s",
        // create all of the nodes for people
    });



    cardDiagram.nodeTemplateMap.add("M",  // male
        $(go.Node, "Vertical",
          { locationSpot: go.Spot.Center, locationObjectName: "ICON" },
          $(go.Panel,"Horizontal",
          { background: "#90D3ED" },
            { name: "ICON" },
            $(go.Picture,
            {
              name: "Picture",
              desiredSize: new go.Size(40, 40),
              margin: new go.Margin(5, 5, 5, 5),
            },
            // new go.Binding("source", "key", m_findHeadShot)),
            new go.Binding("source", "img")),
            $(go.TextBlock, { textAlign: "center",editable: true, stroke: "#104A73", isMultiline: false, cursor: "pointer",font: "16pt Segoe UI,sans-serif", maxSize: new go.Size(150, NaN) },new go.Binding("text", "n",get_name)),
            

            $(go.Panel,
              { // for each attribute show a Shape at a particular place in the overall square
                itemTemplate:
                  $(go.Panel,
                    $(go.Shape,
                      { stroke: null,name: "SHAPE", stroke: "#104A73", cursor: "pointer",fill:"white", strokeWidth: 0 },
                      new go.Binding("fill", "", attrFill),
                      new go.Binding("geometry", "", maleGeometry))
                  ),
                margin: 1
              },
              new go.Binding("itemArray", "a")
            )
          )
        )
      );
      cardDiagram.nodeTemplateMap.add("F",  // female
        $(go.Node, "Vertical",
          { locationSpot: go.Spot.Center, locationObjectName: "ICON" },
          $(go.Panel, "Horizontal",
          { background: "#F29DD1" },
            { name: "ICON" },
            $(go.Picture,
            {
              name: "Picture",
              desiredSize: new go.Size(40, 40),
              margin: new go.Margin(5, 5, 5, 5),
            },

            // new go.Binding("source", "key", f_findHeadShot)),
            new go.Binding("source", "img")),

            $(go.TextBlock, { textAlign: "right", stroke: "#104A73", cursor: "pointer",font: "16pt Segoe UI,sans-serif", maxSize: new go.Size(150, NaN) },new go.Binding("text", "n",get_name)),
            $(go.Panel,
              { // for each attribute show a Shape at a particular place in the overall circle
                itemTemplate:
                  $(go.Panel,
                    $(go.Shape,
                      { stroke: null, strokeWidth: 0 },
                      new go.Binding("fill", "", attrFill),
                      new go.Binding("geometry", "", femaleGeometry))
                  ),
                margin: 1
              },
              new go.Binding("itemArray", "a")
            )
          )
        ));


      cardDiagram.nodeTemplateMap.add("am",  // female
        $(go.Node, "Vertical",
          { locationSpot: go.Spot.Center, locationObjectName: "ICON" },
          $(go.Panel, "Horizontal",
          { background: "#dddddd" },
            { name: "ICON" },

            $(go.TextBlock, { textAlign: "right", stroke: "#104A73", cursor: "pointer",font: "16pt Segoe UI,sans-serif", maxSize: new go.Size(150, NaN) },new go.Binding("text", "n",get_name)),
            $(go.Panel,
              { // for each attribute show a Shape at a particular place in the overall circle
                itemTemplate:
                  $(go.Panel,
                    $(go.Shape,
                      { stroke: null, strokeWidth: 0 },
                      new go.Binding("fill", "", attrFill),
                      new go.Binding("geometry", "", femaleGeometry))
                  ),
                margin: 1
              },
              new go.Binding("itemArray", "a")
            )
          )
        ));


      cardDiagram.nodeTemplateMap.add("af",  // female
        $(go.Node, "Vertical",
          { locationSpot: go.Spot.Center, locationObjectName: "ICON" },
          $(go.Panel, "Horizontal",
          { background: "#dddddd" },
            { name: "ICON" },

            $(go.TextBlock, { textAlign: "right", stroke: "#104A73", cursor: "pointer",font: "16pt Segoe UI,sans-serif", maxSize: new go.Size(150, NaN) },new go.Binding("text", "n",get_name)),
            $(go.Panel,
              { // for each attribute show a Shape at a particular place in the overall circle
                itemTemplate:
                  $(go.Panel,
                    $(go.Shape,
                      { stroke: null, strokeWidth: 0 },
                      new go.Binding("fill", "", attrFill),
                      new go.Binding("geometry", "", femaleGeometry))
                  ),
                margin: 1
              },
              new go.Binding("itemArray", "a")
            )
          )
        ));


      // the representation of each label node -- nothing shows on a Marriage Link
      cardDiagram.nodeTemplateMap.add("LinkLabel",
        $(go.Node, { selectable: false, width: 1, height: 1, fromEndSegmentLength: 20 }));


      cardDiagram.linkTemplate =  // for parent-child relationships
        $(go.Link,
          {
            routing: go.Link.Orthogonal, curviness: 0,
            layerName: "Background", selectable: false,
            fromSpot: go.Spot.Bottom, toSpot: go.Spot.Top
          },
          $(go.Shape, { strokeWidth: 3, stroke: "#548C00" })
        );

      cardDiagram.linkTemplateMap.add("Marriage",  // for marriage relationships
        $(go.Link,
          { selectable: false },
          $(go.Shape, { strokeWidth: 3, stroke: "#548C00" })
      ));




      // determine the color for each attribute shape
      function attrFill(a) {
        switch (a) {
          case "A": return "green";
          case "B": return "orange";
          case "C": return "red";
          case "D": return "cyan";
          case "E": return "gold";
          case "F": return "pink";
          case "G": return "blue";
          case "H": return "brown";
          case "I": return "purple";
          case "J": return "chartreuse";
          case "K": return "lightgray";
          case "L": return "magenta";
          case "S": return "red";
          default: return "transparent";
        }
      }

    var tlsq = go.Geometry.parse("F M1 1 l19 0 0 19 -19 0z");
    var trsq = go.Geometry.parse("F M20 1 l19 0 0 19 -19 0z");
    var brsq = go.Geometry.parse("F M20 20 l19 0 0 19 -19 0z");
    var blsq = go.Geometry.parse("F M1 20 l19 0 0 19 -19 0z");
    var slash = go.Geometry.parse("F M38 0 L40 0 40 2 2 40 0 40 0 38z");
    function maleGeometry(a) {
      switch (a) {
        case "A": return tlsq;
        case "B": return tlsq;
        case "C": return tlsq;
        case "D": return trsq;
        case "E": return trsq;
        case "F": return trsq;
        case "G": return brsq;
        case "H": return brsq;
        case "I": return brsq;
        case "J": return blsq;
        case "K": return blsq;
        case "L": return blsq;
        case "S": return slash;
        default: return tlsq;
      }
    }

      var tlarc = go.Geometry.parse("F M20 20 B 180 90 20 20 19 19 z");
      var trarc = go.Geometry.parse("F M20 20 B 270 90 20 20 19 19 z");
      var brarc = go.Geometry.parse("F M20 20 B 0 90 20 20 19 19 z");
      var blarc = go.Geometry.parse("F M20 20 B 90 90 20 20 19 19 z");
      function femaleGeometry(a) {
        switch (a) {
          case "A": return tlarc;
          case "B": return tlarc;
          case "C": return tlarc;
          case "D": return trarc;
          case "E": return trarc;
          case "F": return trarc;
          case "G": return brarc;
          case "H": return brarc;
          case "I": return brarc;
          case "J": return blarc;
          case "K": return blarc;
          case "L": return blarc;
          case "S": return slash;
          default: return tlarc;
        }
      }
     function get_name(name){
        return name;
     }
      myDiagram.nodeTemplateMap.add("M",  // male
        $(go.Node, "Vertical",
          { locationSpot: go.Spot.Center, locationObjectName: "ICON" },
          $(go.Panel,"Horizontal",
          { background: "#90D3ED" },
            { name: "ICON" },
            $(go.Picture,
            {
              name: "Picture",
              desiredSize: new go.Size(40, 40),
              margin: new go.Margin(5, 5, 5, 5),
            },
            // new go.Binding("source", "key", m_findHeadShot)),
            new go.Binding("source", "img")),
            $(go.TextBlock, { textAlign: "center",editable: true, stroke: "#104A73", isMultiline: false, cursor: "pointer",font: "16pt Segoe UI,sans-serif", maxSize: new go.Size(150, NaN) },new go.Binding("text", "n",get_name)),
            

            $(go.Panel,
              { // for each attribute show a Shape at a particular place in the overall square
                itemTemplate:
                  $(go.Panel,
                    $(go.Shape,
                      { stroke: null,name: "SHAPE", stroke: "#104A73", cursor: "pointer",fill:"white", strokeWidth: 0 },
                      new go.Binding("fill", "", attrFill),
                      new go.Binding("geometry", "", maleGeometry))
                  ),
                margin: 1
              },
              new go.Binding("itemArray", "a")
            )
          )
        )
      );
      myDiagram.nodeTemplateMap.add("F",  // female
        $(go.Node, "Vertical",
          { locationSpot: go.Spot.Center, locationObjectName: "ICON" },
          $(go.Panel, "Horizontal",
          { background: "#F29DD1" },
            { name: "ICON" },
            $(go.Picture,
            {
              name: "Picture",
              desiredSize: new go.Size(40, 40),
              margin: new go.Margin(5, 5, 5, 5),
            },

            // new go.Binding("source", "key", f_findHeadShot)),
            new go.Binding("source", "img")),

            $(go.TextBlock, { textAlign: "right", stroke: "#104A73", cursor: "pointer",font: "16pt Segoe UI,sans-serif", maxSize: new go.Size(150, NaN) },new go.Binding("text", "n",get_name)),
            $(go.Panel,
              { // for each attribute show a Shape at a particular place in the overall circle
                itemTemplate:
                  $(go.Panel,
                    $(go.Shape,
                      { stroke: null, strokeWidth: 0 },
                      new go.Binding("fill", "", attrFill),
                      new go.Binding("geometry", "", femaleGeometry))
                  ),
                margin: 1
              },
              new go.Binding("itemArray", "a")
            )
          )
        ));


      myDiagram.nodeTemplateMap.add("am",  // female
        $(go.Node, "Vertical",
          { locationSpot: go.Spot.Center, locationObjectName: "ICON" },
          $(go.Panel, "Horizontal",
          { background: "#dddddd" },
            { name: "ICON" },

            $(go.TextBlock, { textAlign: "right", stroke: "#104A73", cursor: "pointer",font: "16pt Segoe UI,sans-serif", maxSize: new go.Size(150, NaN) },new go.Binding("text", "n",get_name)),
            $(go.Panel,
              { // for each attribute show a Shape at a particular place in the overall circle
                itemTemplate:
                  $(go.Panel,
                    $(go.Shape,
                      { stroke: null, strokeWidth: 0 },
                      new go.Binding("fill", "", attrFill),
                      new go.Binding("geometry", "", femaleGeometry))
                  ),
                margin: 1
              },
              new go.Binding("itemArray", "a")
            )
          )
        ));


      myDiagram.nodeTemplateMap.add("af",  // female
        $(go.Node, "Vertical",
          { locationSpot: go.Spot.Center, locationObjectName: "ICON" },
          $(go.Panel, "Horizontal",
          { background: "#dddddd" },
            { name: "ICON" },

            $(go.TextBlock, { textAlign: "right", stroke: "#104A73", cursor: "pointer",font: "16pt Segoe UI,sans-serif", maxSize: new go.Size(150, NaN) },new go.Binding("text", "n",get_name)),
            $(go.Panel,
              { // for each attribute show a Shape at a particular place in the overall circle
                itemTemplate:
                  $(go.Panel,
                    $(go.Shape,
                      { stroke: null, strokeWidth: 0 },
                      new go.Binding("fill", "", attrFill),
                      new go.Binding("geometry", "", femaleGeometry))
                  ),
                margin: 1
              },
              new go.Binding("itemArray", "a")
            )
          )
        ));


      // the representation of each label node -- nothing shows on a Marriage Link
      myDiagram.nodeTemplateMap.add("LinkLabel",
        $(go.Node, { selectable: false, width: 1, height: 1, fromEndSegmentLength: 20 }));


      myDiagram.linkTemplate =  // for parent-child relationships
        $(go.Link,
          {
            routing: go.Link.Orthogonal, curviness: 0,
            layerName: "Background", selectable: false,
            fromSpot: go.Spot.Bottom, toSpot: go.Spot.Top
          },
          $(go.Shape, { strokeWidth: 3, stroke: "#548C00" })
        );

      myDiagram.linkTemplateMap.add("Marriage",  // for marriage relationships
        $(go.Link,
          { selectable: false },
          $(go.Shape, { strokeWidth: 3, stroke: "#548C00" })
      ));
      /**
      * 弹出的添加亲人框点击
      */
      cardDiagram.addDiagramListener("ObjectSingleClicked", function(e) {
        var id = e.subject.part.key;
        if(id<0) {
          card_id = id;
          show_card();return false;
        }
      });
    
      myDiagram.addDiagramListener("ObjectSingleClicked", function(e) {
        var id = e.subject.part.key;
        if(id<0) {
          card_id = id;
          show_card();return false;
        }
        var data       = [];
        data[0]        = {};
        // data[0]        = e.subject.part.Zd;
        data[0]['key'] = id;
        data[0]['n']   = e.subject.part.Zd.n;
        data[0]['s']   = e.subject.part.Zd.s;
        data[0]['m']   = e.subject.part.Zd.m;
        data[0]['f']   = e.subject.part.Zd.f;
        data[0]['img'] = e.subject.part.Zd.img;
        // 1=父亲 2=母亲 3=妻子 4=丈夫 5=儿子 6=女儿
        if(!e.subject.part.Zd.f && !e.subject.part.Zd.m) {
            var mKey = -(id * 10 + 2);
            var fKey = -(id * 10 + 1);
            var mp = { key: mKey, n: "添加母亲", s: "F" };
            var fp = { key: fKey, n: "添加父亲", s: "M", ux: mKey };
            data[0]['m'] = mKey;
            data[0]['f'] = fKey;
            data.push(mp);
            data.push(fp);

        }
        if(!e.subject.part.Zd.ux && !e.subject.part.Zd.hb) {
            if(e.subject.part.Zd.s == 'M') {
                var Key = -(id * 10 + 3);
                var m = { key: Key, n: "添加妻子", s: "F" };
                data[0]['ux'] = Key;
                data.push(m);
            }else if(e.subject.part.Zd.s == 'F') {
                var Key = -(id * 10 + 4);
                var m = { key: Key, n: "添加丈夫", s: "M", ux: id };
                data[0]['ux'] = 0;
                data.push(m);
            }
            cardDiagram.model =
            go.GraphObject.make(go.GraphLinksModel,
            { // declare support for link label nodes
                linkLabelKeysProperty: "labelKeys",
                // this property determines which template is used
                nodeCategoryProperty: "s",
                // create all of the nodes for people
                nodeDataArray: data
            });
            show();
            setupMarriages(cardDiagram);
            setupParents(cardDiagram);
        }else {
            if(e.subject.part.Zd.s == 'F') {
                var Key = e.subject.part.Zd.hb;
                var m = { key: Key, n: "丈夫", s: "M", ux: id };
                data.push(m);
                var Key = -(id * 10 + 5);
                var m = { key: Key, n: "添加儿子", s: "M", m: id, f: e.subject.part.Zd.hb };
                data.push(m);
                var Key = -(id * 10 + 6);
                var m = { key: Key, n: "添加女儿", s: "F", m: id, f: e.subject.part.Zd.hb };
                data.push(m);
            }else if(e.subject.part.Zd.s == 'M') {
                var Key = e.subject.part.Zd.ux;
                var m = { key: Key, n: "妻子", s: "F" };
                data[0]['ux'] = Key;
                data.push(m);
                var Key = -(id * 10 + 6);
                var m = { key: Key, n: "添加女儿", s: "F", m: e.subject.part.Zd.ux, f: id };
                data.push(m);
                var Key = -(id * 10 + 5);
                var m = { key: Key, n: "添加儿子", s: "M", m: e.subject.part.Zd.ux, f: id };
                data.push(m);
            }
            cardDiagram.model =
            go.GraphObject.make(go.GraphLinksModel,
            { // declare support for link label nodes
                linkLabelKeysProperty: "labelKeys",
                // this property determines which template is used
                nodeCategoryProperty: "s",
                // create all of the nodes for people
                nodeDataArray: data
            });
            show();
            setupMarriages(cardDiagram);
            setupParents(cardDiagram);
        }
        console.log(data);
        

        // layer.confirm('请选择您的操作？', {
        //   btn: ['添加亲人','查看/修改名片'] //按钮
        // }, function(){
        //   set_session('card_id',id);
        //   window.top.location.href="./personnel_create.html";
        // }, function(){
        //   set_session('card_id',id);
        //   window.top.location.href="./card.html";
        //   }
        // );
      });
    }


    function show() {
      $('#show').modal();
    }
    // create and initialize the Diagram.model given an array of node data representing people
    function setupDiagram(diagram, array, focusId, max_layer) {
      for(k in array) {
        if(!array[k]['m'] && !array[k]['f'] && array[k]['layer'] == max_layer) {
          var mKey = -(array[k]['key'] * 10 + 2);
          var fKey = -(array[k]['key'] * 10 + 1);
          var mp = { key: mKey, n: "添加母亲", s: "am", m: 0, f: 0, ux: fKey };
          var fp = { key: fKey, n: "添加父亲", s: "af", m: 0, f: 0, ux: mKey };
          array[k]['m'] = mKey;
          array[k]['f'] = fKey;
          array.push(mp);
          array.push(fp);
        }
      }
      console.log(array);
      diagram.model =
        go.GraphObject.make(go.GraphLinksModel,
          { // declare support for link label nodes
            linkLabelKeysProperty: "labelKeys",
            // this property determines which template is used
            nodeCategoryProperty: "s",
            // create all of the nodes for people
            nodeDataArray: array
          });
      setupMarriages(diagram);
      setupParents(diagram);

      // if (node !== null) {
      //   diagram.select(node);
      //   remove any spouse for the person under focus:
      //   node.linksConnected.each(function(l) {
      //    if (!l.isLabeledLink) return;
      //    l.opacity = 0;
      //    var spouse = l.getOtherNode(node);
      //    spouse.opacity = 0;
      //    spouse.pickable = false;
      //   });
      // }
    }

     // when a node is double-clicked, add a child to it
    function nodeDoubleClick(e, obj) {
      var clicked = obj.part;
      if (clicked !== null) {
        var thisemp = clicked.data;
        myDiagram.startTransaction("add employee");
        var newemp = { key: getNextKey(), n: "Bob", s: "M", m: 0, f: 0, ux: getNextKey(), a: ["C", "H", "L"] };
        myDiagram.model.addNodeData(newemp);
        myDiagram.commitTransaction("add employee");
      }
    }

    function getNextKey() {
      var key = nodeIdCounter;
      nodeIdCounter--;
      return key;
    }
    var nodeIdCounter = -1;

    function findMarriage(diagram, a, b) {  // A and B are node keys
      var nodeA = diagram.findNodeForKey(a);
      var nodeB = diagram.findNodeForKey(b);
      if (nodeA !== null && nodeB !== null) {
        var it = nodeA.findLinksBetween(nodeB);  // in either direction
        while (it.next()) {
          var link = it.value;
          // Link.data.category === "Marriage" means it's a marriage relationship
          if (link.data !== null && link.data.category === "Marriage") return link;
        }
      }
      return null;
    }

    // now process the node data to determine marriages
    function setupMarriages(diagram) {
      var model = diagram.model;
      var nodeDataArray = model.nodeDataArray;
      for (var i = 0; i < nodeDataArray.length; i++) {
        var data = nodeDataArray[i];
        var key = data.key;
        var uxs = data.ux;
        if (uxs !== undefined) {
          if (typeof uxs === "number") uxs = [ uxs ];
          for (var j = 0; j < uxs.length; j++) {
            var wife = uxs[j];
            if (key === wife) {
              // or warn no reflexive marriages
              continue;
            }
            var link = findMarriage(diagram, key, wife);
            if (link === null) {
              // add a label node for the marriage link
              var mlab = { s: "LinkLabel" };
              model.addNodeData(mlab);
              // add the marriage link itself, also referring to the label node
              var mdata = { from: key, to: wife, labelKeys: [mlab.key], category: "Marriage" };
              model.addLinkData(mdata);
            }
          }
        }
        var virs = data.vir;
        if (virs !== undefined) {
          if (typeof virs === "number") virs = [ virs ];
          for (var j = 0; j < virs.length; j++) {
            var husband = virs[j];
            if (key === husband) {
              // or warn no reflexive marriages
              continue;
            }
            var link = findMarriage(diagram, key, husband);
            if (link === null) {
              // add a label node for the marriage link
              var mlab = { s: "LinkLabel" };
              model.addNodeData(mlab);
              // add the marriage link itself, also referring to the label node
              var mdata = { from: key, to: husband, labelKeys: [mlab.key], category: "Marriage" };
              model.addLinkData(mdata);
            }
          }
        }
      }
    }

    // process parent-child relationships once all marriages are known
    function setupParents(diagram) {
      var model = diagram.model;
      var nodeDataArray = model.nodeDataArray;
      for (var i = 0; i < nodeDataArray.length; i++) {
        var data = nodeDataArray[i];
        var key = data.key;
        var mother = data.m;
        var father = data.f;
        if (mother !== undefined && father !== undefined) {
          var link = findMarriage(diagram, mother, father);
          if (link === null) {
            // or warn no known mother or no known father or no known marriage between them
            if (window.console) window.console.log("unknown marriage: " + mother + " & " + father);
            continue;
          }
          var mdata = link.data;
          var mlabkey = mdata.labelKeys[0];
          var cdata = { from: mlabkey, to: key };
          diagram.model.addLinkData(cdata);
        }
      }
    }

    // A custom layout that shows the two families related to a person's parents
    function GenogramLayout() {
      go.LayeredDigraphLayout.call(this);
      this.initializeOption = go.LayeredDigraphLayout.InitDepthFirstIn;
      this.spouseSpacing = 30;  // minimum space between spouses
    }
    go.Diagram.inherit(GenogramLayout, go.LayeredDigraphLayout);

    /** @override */
    GenogramLayout.prototype.makeNetwork = function(coll) {
      // generate LayoutEdges for each parent-child Link
      var net = this.createNetwork();
      if (coll instanceof go.Diagram) {
        this.add(net, coll.nodes, true);
        this.add(net, coll.links, true);
      } else if (coll instanceof go.Group) {
        this.add(net, coll.memberParts, false);
      } else if (coll.iterator) {
        this.add(net, coll.iterator, false);
      }
      return net;
    };

    GenogramLayout.prototype.add = function(net, coll, nonmemberonly) {
      var multiSpousePeople = new go.Set();
      // consider all Nodes in the given collection
      var it = coll.iterator;
      while (it.next()) {
        var node = it.value;
        if (!(node instanceof go.Node)) continue;
        if (!node.isLayoutPositioned || !node.isVisible()) continue;
        if (nonmemberonly && node.containingGroup !== null) continue;
        // if it's an unmarried Node, or if it's a Link Label Node, create a LayoutVertex for it
        if (node.isLinkLabel) {
          // get marriage Link
          var link = node.labeledLink;
          var spouseA = link.fromNode;
          var spouseB = link.toNode;
          // create vertex representing both husband and wife
          var vertex = net.addNode(node);
          // now define the vertex size to be big enough to hold both spouses
          vertex.width = spouseA.actualBounds.width + this.spouseSpacing + spouseB.actualBounds.width;
          vertex.height = Math.max(spouseA.actualBounds.height, spouseB.actualBounds.height);
          vertex.focus = new go.Point(spouseA.actualBounds.width + this.spouseSpacing / 2, vertex.height / 2);
        } else {
          var marriages = 0;
          node.linksConnected.each(function(l) { if (l.isLabeledLink) marriages++; });
          if (marriages === 0) {
            var vertex = net.addNode(node);
          } else if (marriages > 1) {
            multiSpousePeople.add(node);
          }
        }
      }
      // now do all Links
      it.reset();
      while (it.next()) {
        var link = it.value;
        if (!(link instanceof go.Link)) continue;
        if (!link.isLayoutPositioned || !link.isVisible()) continue;
        if (nonmemberonly && link.containingGroup !== null) continue;
        // if it's a parent-child link, add a LayoutEdge for it
        if (!link.isLabeledLink) {
          var parent = net.findVertex(link.fromNode);  // should be a label node
          var child = net.findVertex(link.toNode);
          if (child !== null) {  // an unmarried child
            net.linkVertexes(parent, child, link);
          } else {  // a married child
            link.toNode.linksConnected.each(function(l) {
              if (!l.isLabeledLink) return;  // if it has no label node, it's a parent-child link
              // found the Marriage Link, now get its label Node
              var mlab = l.labelNodes.first();
              var mlabvert = net.findVertex(mlab);
              if (mlabvert !== null) {
                net.linkVertexes(parent, mlabvert, link);
              }
            });
          }
        }
      }

      while (multiSpousePeople.count > 0) {
        // find all collections of people that are indirectly married to each other
        var node = multiSpousePeople.first();
        var cohort = new go.Set();
        this.extendCohort(cohort, node);
        // then encourage them all to be the same generation by connecting them all with a common vertex
        var dummyvert = net.createVertex();
        net.addVertex(dummyvert);
        var marriages = new go.Set();
        cohort.each(function(n) {
          n.linksConnected.each(function(l) {
            marriages.add(l);
          })
        });
        marriages.each(function(link) {
          // find the vertex for the marriage link (i.e. for the label node)
          var mlab = link.labelNodes.first()
          var v = net.findVertex(mlab);
          if (v !== null) {
            net.linkVertexes(dummyvert, v, null);
          }
        });
        // done with these people, now see if there are any other multiple-married people
        multiSpousePeople.removeAll(cohort);
      }
    };

    // collect all of the people indirectly married with a person
    GenogramLayout.prototype.extendCohort = function(coll, node) {
      if (coll.contains(node)) return;
      coll.add(node);
      var lay = this;
      node.linksConnected.each(function(l) {
        if (l.isLabeledLink) {  // if it's a marriage link, continue with both spouses
          lay.extendCohort(coll, l.fromNode);
          lay.extendCohort(coll, l.toNode);
        }
      });
    };

    /** @override */
    GenogramLayout.prototype.assignLayers = function() {
      go.LayeredDigraphLayout.prototype.assignLayers.call(this);
      var horiz = this.direction == 0.0 || this.direction == 180.0;
      // for every vertex, record the maximum vertex width or height for the vertex's layer
      var maxsizes = [];
      this.network.vertexes.each(function(v) {
        var lay = v.layer;
        var max = maxsizes[lay];
        if (max === undefined) max = 0;
        var sz = (horiz ? v.width : v.height);
        if (sz > max) maxsizes[lay] = sz;
      });
      // now make sure every vertex has the maximum width or height according to which layer it is in,
      // and aligned on the left (if horizontal) or the top (if vertical)
      this.network.vertexes.each(function(v) {
        var lay = v.layer;
        var max = maxsizes[lay];
        if (horiz) {
          v.focus = new go.Point(0, v.height / 2);
          v.width = max;
        } else {
          v.focus = new go.Point(v.width / 2, 0);
          v.height = max;
        }
      });
    };

    /** @override */
    GenogramLayout.prototype.commitNodes = function() {   
      go.LayeredDigraphLayout.prototype.commitNodes.call(this);
      // position regular nodes
      this.network.vertexes.each(function(v) {
        if (v.node !== null && !v.node.isLinkLabel) {
          v.node.position = new go.Point(v.x, v.y);
        }
      });
      // position the spouses of each marriage vertex
      var layout = this;
      this.network.vertexes.each(function(v) {
        if (v.node === null) return;
        if (!v.node.isLinkLabel) return;
        var labnode = v.node;
        var lablink = labnode.labeledLink;
        lablink.invalidateRoute();
        var spouseA = lablink.fromNode;
        var spouseB = lablink.toNode;
        // prefer fathers on the left, mothers on the right
        if (spouseA.data.s === "F") {  // sex is female
          var temp = spouseA;
          spouseA = spouseB;
          spouseB = temp;
        }
        // see if the parents are on the desired sides, to avoid a link crossing
        var aParentsNode = layout.findParentsMarriageLabelNode(spouseA);
        var bParentsNode = layout.findParentsMarriageLabelNode(spouseB);
        if (aParentsNode !== null && bParentsNode !== null && aParentsNode.position.x > bParentsNode.position.x) {
          // swap the spouses
          var temp = spouseA;
          spouseA = spouseB;
          spouseB = temp;
        }
        spouseA.position = new go.Point(v.x, v.y);
        spouseB.position = new go.Point(v.x + spouseA.actualBounds.width + layout.spouseSpacing, v.y);
        if (spouseA.opacity === 0) {
          var pos = new go.Point(v.centerX - spouseA.actualBounds.width / 2, v.y);
          spouseA.position = pos;
          spouseB.position = pos;
        } else if (spouseB.opacity === 0) {
          var pos = new go.Point(v.centerX - spouseB.actualBounds.width / 2, v.y);
          spouseA.position = pos;
          spouseB.position = pos;
        }
      });
      // position only-child nodes to be under the marriage label node
      this.network.vertexes.each(function(v) {
        if (v.node === null || v.node.linksConnected.count > 1) return;
        var mnode = layout.findParentsMarriageLabelNode(v.node);
        if (mnode !== null && mnode.linksConnected.count === 1) {  // if only one child
          var mvert = layout.network.findVertex(mnode);
          var newbnds = v.node.actualBounds.copy();
          newbnds.x = mvert.centerX - v.node.actualBounds.width / 2;
          // see if there's any empty space at the horizontal mid-point in that layer
          var overlaps = layout.diagram.findObjectsIn(newbnds, function(x) { return x.part; }, function(p) { return p !== v.node; }, true);
          if (overlaps.count === 0) {
            v.node.move(newbnds.position);
          }
        }
      });
    };

    GenogramLayout.prototype.findParentsMarriageLabelNode = function(node) {
      var it = node.findNodesInto();
      while (it.next()) {
        var n = it.value;
        if (n.isLinkLabel) return n;
      }
      return null;
    };

    function has_power() 
    {
      var url = base_url+'index/card/has_power';
      var token = get_token();
      var data = {};
      data['user_token'] = token;
      $.ajax({
          url: url,
          type: 'post',
          data: data,
          headers : {
              'user_token' : token
          },
          dataType: 'json',
          timeout: 1000,
          success: function (data, status) {
              var data = eval("("+data+")"); 
              if(data.code==codeArr['e500'])
              {
                  alert(data.msg);
                  window.location.href = "{:url('Home/index')}";
              }else if(data.code==codeArr['e201'])
              {
                  alert(data.msg);
                  window.location.href = "{:url('Home/index')}";
              }else if(data.code==codeArr['e200'])
              {
                  // imgs = eval("("+data.data.imgs+")"); 
                  console.log(imgs);
                  setupDiagram(myDiagram,strToJson(data['data']['data']),4);
              }else
              {
                  window.location.href = "{:url('Home/index')}";
              }
              
          },
          fail: function (err, status) {
              alert('数据请求异常');
          }
        })
    }

    function show_card() {
      $('#card_modal').modal();
    }


    