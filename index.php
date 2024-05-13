<?php 
include("connect-db.php");

function getInputsFromOutput($recipeID) {
    $res = query("SELECT itemName, quantity, unitPrice FROM input JOIN item on input.itemName=item.name WHERE recipeID=?", $recipeID);
    foreach ($res as $key => $item) {
        $recipeID = query("SELECT recipeID FROM output WHERE itemName=?", $item["itemName"]);
        if(!empty($recipeID))   
            $res[$key]["inputs"] = getInputsFromOutput($recipeID[0]["recipeID"]);
    }
    return $res;
}


if(isset($_POST["ajax"])) {
    header('Content-Type: application/json; charset=utf-8');
    $out = [];

    switch($_POST["function"]) {
        case "save":
            query("UPDATE player SET darkModeOn=? WHERE email=?;", $_POST["darkmode"] === "true" ? 1 : 0, $_POST["email"]);
            break;

        case "generateFullRecipe":
            $recipeID = query("SELECT recipeID FROM output WHERE itemName=?", $_POST["finalOutput"])[0]["recipeID"];
            $item = query("SELECT * FROM item WHERE name=?", $_POST["finalOutput"])[0];
            $out["itemName"] = $item["name"];
            $out["unitPrice"] = $item["unitPrice"];
            $out["inputs"] = getInputsFromOutput($recipeID);
            $out = [$out];

            query("DELETE FROM in_player_history WHERE playerEmail=? AND recipeID=?", $_POST["email"], $recipeID);
            query("INSERT INTO in_player_history (playerEmail, recipeID) VALUES (?, ?)", $_POST["email"], $recipeID);
            break;
        case "updateInventory":
            if($_POST["val"] === "0") {
                query("DELETE FROM owns_item WHERE playerEmail=? AND itemName=?", $_POST["email"], $_POST["itemName"]);
            }
            else {
                query("INSERT IGNORE INTO owns_item (playerEmail, itemName, quantity) VALUES (?, ?, ?)", $_POST["email"], $_POST["itemName"], $_POST["val"]);
                query("UPDATE owns_item SET quantity=? WHERE playerEmail=? AND itemName=?", $_POST["val"], $_POST["email"], $_POST["itemName"]);
            }
            break;
        case "addHasBonus":
            query("INSERT INTO has_bonus (playerEmail, bonusID) VALUES (?, ?)", $_POST["email"], $_POST["id"]);
            break;
        case "removeHasBonus":
            query("DELETE FROM has_bonus WHERE playerEmail=? AND bonusID=?", $_POST["email"], $_POST["id"]);
            break;
    }
    echo json_encode($out);
    return;
}

// Sources:
// https://stackoverflow.com/questions/37712913/changing-the-url-by-post-method-in-php
// https://stackoverflow.com/questions/4196971/how-to-get-the-html-tag-html-with-javascript-jquery
// https://stackoverflow.com/questions/7667603/change-data-theme-in-jquery-mobile
// https://developer.mozilla.org/en-US/docs/Web
// https://stackoverflow.com/questions/29029390/how-to-make-jointjs-paper-responsive
// https://stackoverflow.com/questions/58288113/how-to-align-the-jointjs-graph-to-the-center-middle-of-the-paper-horizontally
// https://stackoverflow.com/questions/24016394/jointjs-non-interactive-elements
// https://stackoverflow.com/questions/48148989/jointjs-get-all-successors-with-links
// https://stackoverflow.com/questions/11179406/jquery-get-value-of-select-onchange
// https://stackoverflow.com/questions/18697843/jquery-get-the-name-of-a-select-option
// https://stackoverflow.com/questions/4592493/check-if-element-exists-in-jquery
// https://stackoverflow.com/questions/596314/jquery-ids-with-spaces
// https://stackoverflow.com/questions/22154714/how-can-i-change-the-attrs-of-a-custom-object-in-jointjs
// https://stackoverflow.com/questions/58801170/jointjs-how-to-change-the-colour-of-a-link-on-hover
// https://stackoverflow.com/questions/4192847/set-scroll-position
// https://stackoverflow.com/questions/45888/what-is-the-most-efficient-way-to-sort-an-html-selects-options-by-value-while
// https://stackoverflow.com/questions/8433691/sorting-list-of-elements-in-jquery
// https://www.w3schools.com/mysql/
// https://stackoverflow.com/questions/6965333/mysql-union-distinct
// https://stackoverflow.com/questions/901712/how-do-i-check-whether-a-checkbox-is-checked-in-jquery
// https://stackoverflow.com/questions/2660323/jquery-checkboxes-and-ischecked
// https://www.geeksforgeeks.org/how-to-insert-row-if-not-exists-in-sql/
// https://stackoverflow.com/questions/6802765/jquery-dealing-with-a-space-in-the-id-attribute
// https://stackoverflow.com/questions/7241878/for-in-loops-in-javascript-key-value-pairs
// https://stackoverflow.com/questions/7486085/copy-array-by-value
// https://github.com/clientIO/joint/discussions/2541

if(isset($_POST["login"])) {
    $emailHits = query("SELECT * FROM player WHERE email=?;", $_POST["email"]);
    if(!empty($emailHits)) {
        if(password_verify($_POST["password"], $emailHits[0]["password"])) {
            $_SESSION["email"] = $_POST["email"];

            $darkmode = $_POST["darkmode"];
            if($darkmode === "true")
                $darkmode = 1;
            else
                $darkmode = 0;
        }
        else {
            header("Location: login.php?status=failure");
            return;
        }
    }
    else {
        header("Location: login.php?status=failure");
        return;
    }
}
else if(isset($_POST["register"])) {
    $emailHits = query("SELECT * FROM player WHERE email=?;", $_POST["email"]);
    if(empty($emailHits)) {
        $darkmode = $_POST["darkmode"];
        if($darkmode === "true")
            $darkmode = 1;
        else
            $darkmode = 0;
        query("INSERT into player (email, password, darkModeOn) VALUES (?, ?, ?);", $_POST["email"], password_hash($_POST["password"], PASSWORD_DEFAULT), $darkmode);
        $_SESSION["email"] = $_POST["email"];
    }
    else {
        if(password_verify($_POST["password"], $emailHits[0]["password"])) {
            $_SESSION["email"] = $_POST["email"];

            $darkmode = $_POST["darkmode"];
            if($darkmode === "true")
                $darkmode = 1;
            else
                $darkmode = 0;
        }
        else {
            header("Location: register.php?status=failure");
            return;
        }
    }
}
else if(!isset($_POST["email"])) {
    header("Location: login.php");
    return;
}

$darkmode = query("SELECT darkModeOn FROM player WHERE email=?;", $_POST["email"])[0]["darkModeOn"] === 1 ? "true" : "";
$finalOutputs = query("SELECT * FROM (
                        SELECT itemName, relativeTimeCreated FROM output JOIN in_player_history on output.recipeID=in_player_history.recipeID WHERE playerEmail=?
                        UNION
                        SELECT itemName, 0 FROM output)
                        aa GROUP BY itemName;", 
                        $_SESSION["email"]);

$bonuses = query("SELECT * FROM bonus LEFT OUTER JOIN has_bonus ON bonus.id=has_bonus.bonusID WHERE playerEmail=? OR playerEmail IS NULL;", $_POST["email"]);

$bonusAffects = [];
foreach (query("SELECT bonusID, itemName FROM bonus_affects JOIN output ON bonus_affects.recipeID=output.recipeID") as $key => $element) {
    if(!isset($bonusAffects[$element["itemName"]])) {
        $bonusAffects[$element["itemName"]] = [];
    }
    array_push($bonusAffects[$element["itemName"]], $element["bonusID"]);
}

$userInventory = query("SELECT * from owns_item WHERE playerEmail=?", $_SESSION["email"]);

?>

<!DOCTYPE html>
<html lang="en" class="h-100">
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1"> 

        <title>Home</title>
        <link rel="icon" type="image/x-icon" href="/favicon.ico">
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
        <link rel="stylesheet/less" type="text/css" href="main.less?ts=\<\?=filemtime('style.css')?"/>
        <script src="less.js" type="text/javascript"></script>
        <script src="jquery.js" type="text/javascript"></script>

        <script src="node_modules/@joint/core/dist/joint.js"></script>
        <script src="node_modules/@dagrejs/graphlib/dist/graphlib.js"></script>
        <script src="node_modules/@dagrejs/dagre/dist/dagre.js"></script>
        <script src="node_modules/@joint/layout-directed-graph/dist/DirectedGraph.js"></script>

        <script>
            const GRAPH_WIDTH = 6000;
            var email = "<?= $_SESSION["email"] ?>";
            var userInventory = JSON.parse('<?=addslashes(json_encode($userInventory))?>');
            var bonuses = JSON.parse('<?=addslashes(json_encode($bonuses))?>');
            var bonusAffects = JSON.parse('<?=addslashes(json_encode($bonusAffects))?>');

            var graph;
            var paper;
            var graphLayout;
            var root;
            var mainTree;
            var treeMap = {};
            var finalQuantity = 1;
            var inventory = [];
            var tempInventory = [];
            var remaining = [];

            function load() {
                localStorage.setItem("darkmode", "<?=$darkmode?>");
                $(".navbar").append("<a class='m-2 btn btn-danger' href='login.php'>Log Out</a>");

                $("#darkmode").on("click", function() {
                    if(localStorage.getItem("darkmode")) {
                        $(this).html("🌙");
                        $(document.querySelector("html")).attr("data-bs-theme", "light");
                        localStorage.setItem("darkmode", "");
                    }
                    else {
                        $(this).html("☀️");
                        $(document.querySelector("html")).attr("data-bs-theme", "dark");
                        localStorage.setItem("darkmode", "true");
                    }

                    $.ajax({
                        method: "POST",
                        data: {"ajax": true, "email": email, "function": "save", "darkmode": localStorage.getItem("darkmode")=="true"}
                    }).done((x) => console.log(x));
                });

                if(!localStorage.getItem("darkmode")) {
                    $("#darkmode").html("🌙");
                    $(document.querySelector("html")).attr("data-bs-theme", "light");
                    localStorage.setItem("darkmode", "");
                }
                else {
                    $("#darkmode").html("☀️");
                    $(document.querySelector("html")).attr("data-bs-theme", "dark");
                    localStorage.setItem("darkmode", "true");
                }

                $("#sort-select").on('change', function() {
                    let chosen = $(this).find('option:selected');
                    sortSelect(chosen.attr("data-method"), parseInt(chosen.attr("data-direction")));
                });

                $('#recipe-select').on('change', function() {
                    if(this.value != "Select a recipe.") {
                        $.ajax({
                            method: "POST",
                            data: {"ajax": true, "function": "generateFullRecipe", "email": email, "finalOutput": $(this).find('option:selected').text() }
                        }).done((x) => {
                            itemName = $(this).find('option:selected').text();
                            mainTree = x;
                            mainTree[0]["quantityNeeded"] = finalQuantity;
                            graph.clear();
                            addRoot();
                            $("#inventory").html("<h3 class='m-0'><u>Inventory</u></h3><h4><u>Before Craft => After Craft</u></h4>");
                            let i = 0;
                            mainTree[0]["inputs"].forEach(element => {
                                populateTree(element, root, i);
                                populateInventory(element, 1);  
                                i++;
                            });

                            $('.inventory-quantity').on('change', function() {
                                let val = parseInt($(this).val());
                                $.ajax({
                                    method: "POST",
                                    data: {"ajax": true, "email": email, "function": "updateInventory", "itemName": $(this).attr("id"), "val": val}
                                }).done((x) => console.log(x));
                                update();
                            });


                            userInventory.forEach(element => {
                                if($("[id='" + element["itemName"] + "']").length !== 0) {
                                    $("[id='" + element["itemName"] + "']").val(element["quantity"]);
                                }
                            });

                            update();

                            $("#left").scrollTop(0);
                            $("#left").scrollLeft(0);//GRAPH_WIDTH/2-$(window).width()*(3/4)/2);
                        });
                    }
                });

                $('#finalQuantity').on('change', function() {
                    if(root !== undefined) {
                        val = parseInt($('#finalQuantity').val());
                        if(val >= 1) {
                            finalQuantity = val;
                            mainTree[0]["quantityNeeded"] = finalQuantity;
                            update();
                        }
                    }
                });

                $('.bonus-check').on('change', function() {
                    if($(this).prop("checked")) 
                        ajaxFunc = "addHasBonus"
                    else
                        ajaxFunc = "removeHasBonus"
                    $.ajax({
                        method: "POST",
                        data: {"ajax": true, "email": email, "function": ajaxFunc, "id": $(this).attr("id")}
                    }).done((x) => console.log(x));

                    if(root !== undefined) {
                        update();
                    }
                })

                ///////////////////////////////////////////////////

                var namespace = joint.shapes;

                graph = new joint.dia.Graph({}, { cellNamespace: namespace });

                paper = new joint.dia.Paper({
                    el: document.getElementById('myholder'),
                    model: graph,
                    width: GRAPH_WIDTH,
                    height: 1500,
                    gridSize: 1,
                    cellViewNamespace: namespace,
                    interactive: false
                });
                paper.$el.css('pointer-events', 'none');

                // joint.elementTools.AddButton = joint.elementTools.Button.extend({
                //     name: 'add-button',
                //     options: {
                //         markup: [{
                //             tagName: 'circle',
                //             selector: 'button',
                //             attributes: {
                //                 'r': 7,
                //                 'fill': 'grey',
                //                 'cursor': 'pointer'
                //             }
                //         }, {
                //             tagName: 'path',
                //             selector: 'icon',
                //             attributes: {
                //                 'd': 'M -4.8 0 L 4.8 0 M 0 -4.8 L 0 4.8',
                //                 'fill': 'none',
                //                 'stroke': '#FFFFFF',
                //                 'stroke-width': 2,
                //                 'pointer-events': 'none'
                //             }
                //         }],
                //         x: '0%',
                //         y: '0%',
                //         rotate: true,
                //         action: function(evt) {
                //             alert('View id: ' + this.id + '\n' + 'Model id: ' + this.model.id);
                //         }
                //     }
                // });

                // joint.elementTools.SubButton = joint.elementTools.Button.extend({
                //     name: 'sub-button',
                //     options: {
                //         markup: [{
                //             tagName: 'circle',
                //             selector: 'button',
                //             attributes: {
                //                 'r': 7,
                //                 'fill': 'grey',
                //                 'cursor': 'pointer'
                //             }
                //         }, {
                //             tagName: 'path',
                //             selector: 'icon',
                //             attributes: {
                //                 'd': 'M -4.8 0 L 4.8 0',
                //                 'fill': 'none',
                //                 'stroke': '#FFFFFF',
                //                 'stroke-width': 2,
                //                 'pointer-events': 'none'
                //             }
                //         }],
                //         x: '0%',
                //         y: '100%',
                //         rotate: true,
                //         action: function(evt) {
                            
                //         }
                //     }
                // });
            }

            function save() {
                
            }

            function addRoot() {
                root = new joint.shapes.standard.Rectangle();
                root.resize(120, 60);
                root.attr({
                    body: {
                        fill: '#1a51a4'
                    },
                    label: {
                        text: "",
                        fill: 'white'
                    }
                });
                root.addTo(graph);
                mainTree[0]["node"] = root;
                treeMap[root.id] = [0];

                const paperArea = paper.getArea();
                const contentArea = paper.getContentArea();

                root.position((paperArea.width - contentArea.width) / 2, 50);

                //graphLayout.layout();
            }

            function addChild(parent, name, place) {
                let rect = new joint.shapes.standard.Rectangle();
                rect.resize(120, 60);
                rect.attr({
                    body: {
                        fill: '#1a51a4'
                    },
                    label: {
                        text: "",
                        fill: 'white'
                    }
                });
                rect.addTo(graph);
                treeMap[rect.id] = treeMap[parent.id].concat([place]);

                // var addButton = new joint.elementTools.AddButton();
                // var subButton = new joint.elementTools.SubButton();
                // var toolsView = new joint.dia.ToolsView({
                //     tools: [addButton, subButton]
                // });
                
                // var elementView = rect.findView(paper);
                // elementView.addTools(toolsView);

                var link = new joint.shapes.standard.Link();
                link.attr('line/stroke', 'grey');
                link.source(parent);
                link.target(rect);
                link.addTo(graph);

                //graphLayout.layout();
                
                return rect;
            }

            function populateTree(tree, parent, place) {
                let child = addChild(parent, tree["itemName"], place);
                tree["node"] = child;
                if(tree["inputs"]) {
                    let i = 0;
                    tree["inputs"].forEach(element => {
                        populateTree(element, child, i);
                        i++;
                    });
                }
            }

            function update() {
                updateInventory();

                tempInventory = $.extend(true, [], inventory);
                updateNodes();

                remaining = [];
                updateRemaining();
                remaining = tempInventory;
                for (var key in remaining) {
                    if(remaining.hasOwnProperty(key))
                        $("[id='+" + key + "']").html(remaining[key]);
                };
            }

            function updateInventory() {
                inventory = [];
                $('.inventory-quantity').each(function() {
                    val = parseInt($(this).val());
                    if(val > 0) {
                        inventory[$(this).attr("id")] = val;
                    }
                    else {
                        inventory[$(this).attr("id")] = 0;
                    }
                });
            }

            function updateNodes(tree=mainTree[0], parent=null) {
                // "IF NOT ROOT"
                if(parent) {
                    tree["quantityNeeded"] = parent["quantityNeeded"] * tree["quantity"];
                    if(parent["bonusFactor"])
                        tree["quantityNeeded"] /= parent["bonusFactor"];
                    tree["quantityNeeded"] = Math.ceil(tree["quantityNeeded"]);

                    tempQ = tree["quantityNeeded"];
                    if(tempInventory[tree["itemName"]] > 0) {
                        let change = Math.min(tree["quantityNeeded"], tempInventory[tree["itemName"]]);
                        tree["quantityNeeded"] -= change;
                    }
                    tempInventory[tree["itemName"]] -= tempQ;
                }

                label = "x" + tree["quantityNeeded"] + "\n" + tree["itemName"] + "\n🟡 " + tree["unitPrice"] + " per";
                tree["node"].attr("label/text", label);
                
                if(tree["inputs"]) {
                    tree["bonusFactor"] = 1;
                    bonusAffects[tree["itemName"]].forEach(bonus => {
                        if($("#"+bonus)[0].checked)
                            tree["bonusFactor"] *= 1 + bonuses[bonus-1]["percentage"];
                    });

                    tree["inputs"].forEach(input => {
                        updateNodes(input, tree);
                    });
                }

                graphLayout = joint.layout.DirectedGraph.layout(graph, {
                    rankSep: 100,
                    nodeSep: 40,
                    edgeSep: 40,
                    rankDir: "TB",
                    marginX: 50,
                    marginY: 50,
                    setVertices: false
                });

            }

            function populateInventory(tree, level) {
                let ordinals = ["First", "Second", "Third", "Fourth", "Fifth", "Sixth", "Seventh", "Eighth", "Ninth", "Tenth"];
                if($("#"+ordinals[level]).length === 0)
                    $("#inventory").append("<div class='pb-2' id='" + ordinals[level] + "'><h5>" + ordinals[level] + " Level" + "<h5></div>")

                if($("[id='" + tree["itemName"] + "']").length === 0)
                    $("#"+ordinals[level]).append("<div class='ps-2 d-flex d-columns justify-content-between'>" + 
                                                        "<p class='m-0 w-50'>" + tree["itemName"] + "</p>" + 
                                                        "<div class='w-50 me-1 d-flex d-columns'>" +
                                                            "<input  id='" + tree["itemName"] +  "' type='number' class='m-0 inventory-quantity justify-self-start' value='0' min='0' style='width: 80px;'></input>" + 
                                                            "<p class='m-1 justify-self-start'>" + "=></p>" + 
                                                            "<p class='m-1 remaining-val justify-self-end' id='+" + tree["itemName"] +  "' >" + "b</p>" + 
                                                        "</div>" +
                                                    "</div>");

                if(tree["inputs"]) {
                    tree["inputs"].forEach(element => {
                        populateInventory(element, level+1);
                    });
                }
            }

            function updateRemaining(tree=mainTree[0]) {
                if(!remaining[tree["itemName"]])
                    remaining[tree["itemName"]] = 0;
                remaining[tree["itemName"]] += tree["quantityNeeded"];
                if(tree["inputs"]) {
                    tree["inputs"].forEach(input => {
                        updateRemaining(input);
                    });
                }
            }

            function sortSelect(method, direction) {
                let options = $(".select-option");
                let defaultOption = $("#default-select");

                switch (method) {
                    case "alphabetical":
                        options.sort(function(a, b) {
                            if(a.text > b.text) return direction;
                            if(a.text < b.text) return direction*-1;
                            return 0;
                        });
                        break;
                    case "recency":
                        options.sort(function(a, b) {
                            intA = parseInt(a.getAttribute("data-recency"));
                            intB = parseInt(b.getAttribute("data-recency"));

                            if(intA == 0) {
                                intA = -Number.MAX_VALUE/2*direction;
                            }
                            if(intB == 0) {
                                intB = -Number.MAX_VALUE/2*direction;
                            }

                            if(intA > intB) return -direction;
                            if(intA < intB) return -direction*-1;
                            return 0;
                        });
                        break;
                }
                $("#recipe-select").empty().append(defaultOption);
                $("#recipe-select").append(options);
            }
        
        </script>

<body onload="load();" onunload="save();" class="h-100 w-100">
    <div class="h-100 d-flex flex-column w-100">
        <?php include("header.php");?>

        <div class="modal fade" id="deleteModal" tabindex="-1">
            <div class="modal-dialog">
                <div class="modal-content">
                <div class="modal-header">
                    <h1 class="modal-title fs-5" id="exampleModalLabel">Delete Account</h1>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    Are you sure?
                </div>
                <form class="modal-footer" action="login.php" method="post">
                    <input type="hidden" name="deleteAccount"></input>
                    <button class="btn btn-danger" type="submit" data-bs-toggle="modal">Delete Account</button>
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                </form>
                </div>
            </div>
        </div>
        
        <div class="flex-grow-1 d-flex flex-rows overflow-scroll" id="main">
            <div class="border-end border-light-subtle border-5 col-9 align-self-stretch overflow-scroll" id="left">
            
                <div class="dropdown d-flex d-rows">
                    <select id="sort-select" class="form-select w-25 bg-dark-subtle">
                        <option data-method="alphabetical" data-direction="1">Alphabetical (Ascending)</option>
                        <option data-method="alphabetical" data-direction="-1">Alphabetical (Descending)</option>
                        <option data-method="recency" data-direction="1">Previous History (Ascending)</option>
                        <option data-method="recency" data-direction="-1">Previous History (Descending)</option>
                    </select>
                    <select id="recipe-select" class="form-select w-25 bg-dark-subtle" aria-label="Default select example">
                        <option id="default-select" selected>Select a recipe.</option>
                        <?php foreach ($finalOutputs as $key => $output) { ?>
                            <option class="select-option" value="<?=$key?>" data-recency="<?=$output["relativeTimeCreated"]?>"><?=$output["itemName"]?></option>
                        <?php }?>
                    </select>
                    <input id="finalQuantity" type="number" class="pr-5 bg-dark-subtle border border-0 rounded" placeholder="quantity" min="1"></number>
                </div>

                <div id="myholder" class=""></div>
            </div>

            <div class="p-3 border-start border-dark-subtle border-5 col-3 align-self-stretch overflow-scroll">
                <div class="mb-4" id="inventory">
                    <h3 class='m-0'><u>Inventory</u></h3>
                    <h4><u>Before Craft => After Craft</u></h4>
                </div>
                <div class="d-flex flex-column" id="bonuses">
                    <h3><u>Bonuses</u></h3>
                    <?php foreach($bonuses as $key => $bonus) { ?>
                        <div>
                            <input class="form-check-input bonus-check" id="<?=$bonus["id"]?>" type="checkbox" <?=$bonus["playerEmail"]==$_SESSION["email"] ? "checked" : "";?>>
                            <label class="form-check-label" ><?=$bonus["name"]?></label>
                        </div>
                    <?php }?>
                </div>
                <button class="mt-5 btn btn-danger" data-bs-toggle="modal" data-bs-target="#deleteModal">Delete Account</button>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.8/dist/umd/popper.min.js" integrity="sha384-I7E8VVD/ismYTF4hNIPjVp/Zjvgyol6VFvRkX/vR+Vc4jQkC+hVqc2pM8ODewa9r" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.min.js" integrity="sha384-0pUGZvbkm6XF6gxjEnlmuGrJXVbNuzT9qBBavbLwCsOGabYfZo0T0to5eqruptLy" crossorigin="anonymous"></script>
</body>
</html>
