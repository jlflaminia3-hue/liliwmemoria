@extends('home.home_master')
@section('home')
<style>
    .visit-page { padding-top: 120px !important; }
    #locatorMap { 
        height: 420px; 
        width: 100%; 
        border-radius: 6px; 
        overflow: hidden;
        position: relative;
        z-index: 1;
    }
    .container { max-width: 1060px; margin: 0 auto; }
    .locator-badge { display: inline-flex; align-items: center; gap: .5rem; padding: .4rem .65rem; border-radius: 999px; background: #f8f9fa; border: 1px solid #e9ecef; }
    .marker-entrance { display: inline-flex; align-items: center; gap: 8px; }
    .marker-entrance-dot { width: 16px; height: 16px; border-radius: 999px; background: #dc3545; border: 3px solid #fff; box-shadow: 0 6px 16px rgba(0,0,0,.25); }
    .marker-entrance-label { background: rgba(220,53,69,.95); color: #fff; border-radius: 999px; padding: 6px 10px; font-weight: 700; box-shadow: 0 6px 16px rgba(0,0,0,.2); white-space: nowrap; }
    .marker-lot { 
        background: #0d6efd; 
        color: #fff; 
        border-radius: 8px; 
        padding: 8px 12px; 
        font-weight: 700;
        font-size: 13px;
        white-space: nowrap;
        box-shadow: 0 2px 8px rgba(13, 110, 253, 0.4), 0 4px 12px rgba(0,0,0,.15); 
        border: 2px solid #fff;
        text-align: center;
        letter-spacing: 0.5px;
    }
    #locatorMap .leaflet-control-container { position: absolute !important; top: 0 !important; left: 0 !important; right: 0 !important; bottom: 0 !important; pointer-events: none; }
    #locatorMap .leaflet-control { pointer-events: auto; }
    #locatorMap .leaflet-control-zoom { position: absolute !important; top: 12px !important; left: 12px !important; z-index: 999 !important; }
</style>

<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />

<section class="lonyo-section-padding6 visit-page lonyo-hero-section light-bg liliwmemoria-hero-bg">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-11">

                <div class="card border-0 shadow-sm">
                    <div class="card-body p-3 p-md-4">

                        <div class="d-flex flex-wrap align-items-center justify-content-between gap-2 mb-3">
                            <div>
                                <h3 class="mb-1">Tomb Locator</h3>
                                <div class="text-muted">
                                    Visitor: <span class="fw-semibold">{{ $log->visitor_name }}</span> •
                                    Visiting: <span class="fw-semibold">{{ $deceased->last_name }}, {{ $deceased->first_name }}</span>
                                </div>
                            </div>
                            <div class="d-flex gap-2 flex-wrap">
                                <a href="{{ route('public.visit.create') }}" class="btn btn-outline-secondary">New Visitor</a>
                                <a href="{{ route('public.map') }}" class="btn btn-outline-secondary">Public Map</a>
                            </div>
                        </div>

                        <div class="d-flex flex-wrap gap-2 mb-3">
                            <span class="locator-badge">
                                <span class="fw-semibold">{{ $entrance['label'] }}</span>
                                <span class="text-muted">→</span>
                                <span class="fw-semibold">Lot {{ $lot->lot_id }}</span>
                            </span>
                            @if ($lot->block)
                                <span class="locator-badge">Block <span class="fw-semibold">{{ $lot->block }}</span></span>
                            @endif
                            @if ($lot->lot_category_label)
                                <span class="locator-badge">{{ $lot->lot_category_label }}</span>
                            @endif
                        </div>

                        <div id="locatorMap"></div>

                        <div class="alert alert-info mt-3 mb-0">
                            <div class="fw-semibold mb-1">Guide</div>
                            <div class="text-muted">
                                The location of <span class="fw-semibold">Lot {{ $lot->lot_id }}</span> is shown on the map (highlighted in red).
                                Please use the yellow lane as your designated walkway for safe and orderly passage.
                                If you need help locating the tomb, please ask the cemetery office.
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>
</section>

<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function () {
    var imageUrl = @json($mapImageUrl);
    var entrance = @json($entrance);
    var lanes = @json($lanes ?? []);
    var lanesSnapDistance = Number(@json($lanesSnapDistance ?? 80));
    var lot = {!! json_encode([
        'lot_id' => $lot->lot_id,
        'geometry_type' => $lot->geometry_type,
        'geometry' => $lot->geometry,
        'latitude' => $lot->latitude,
        'longitude' => $lot->longitude,
    ], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) !!};

    function loadImageDimensions(src, cb) {
        var img = new Image();
        img.onload = function () { cb({ width: img.naturalWidth, height: img.naturalHeight }); };
        img.onerror = function () { cb({ width: 1000, height: 700 }); };
        img.src = src;
    }

    function entranceIcon() {
        return L.divIcon({
            className: '',
            html: '<div style="width: 24px; height: 24px; border-radius: 50%; background: #dc3545; border: 3px solid #fff; box-shadow: 0 6px 16px rgba(0,0,0,.25); margin-left: -12px; margin-top: -12px;"></div>',
            iconSize: [24, 24],
            iconAnchor: [12, 12],
        });
    }

    function lotIcon() {
        return L.divIcon({
            className: '',
            html: '<div class="marker-lot">Lot ' + lot.lot_id + '</div>',
            iconSize: [100, 40],
            iconAnchor: [50, -5],
        });
    }

    function dist(a, b) {
        var dy = a[0] - b[0];
        var dx = a[1] - b[1];
        return Math.sqrt(dx * dx + dy * dy);
    }

    function closestPointOnSegment(p, a, b) {
        var ay = a[0], ax = a[1];
        var by = b[0], bx = b[1];
        var py = p[0], px = p[1];
        var vx = bx - ax;
        var vy = by - ay;
        var wx = px - ax;
        var wy = py - ay;
        var c1 = vx * wx + vy * wy;
        var c2 = vx * vx + vy * vy;
        var t = c2 === 0 ? 0 : (c1 / c2);
        if (t < 0) t = 0;
        if (t > 1) t = 1;
        return [ay + vy * t, ax + vx * t, t];
    }

    function segmentIntersection(a, b, c, d) {
        // a,b,c,d are [y,x] points. Returns { yx:[y,x], t, u } or null.
        var ax = a[1], ay = a[0];
        var bx = b[1], by = b[0];
        var cx = c[1], cy = c[0];
        var dx = d[1], dy = d[0];

        var rX = bx - ax, rY = by - ay;
        var sX = dx - cx, sY = dy - cy;
        var denom = (rX * sY) - (rY * sX);
        if (Math.abs(denom) < 1e-9) return null; // parallel/collinear

        var qpx = cx - ax, qpy = cy - ay;
        var t = ((qpx * sY) - (qpy * sX)) / denom;
        var u = ((qpx * rY) - (qpy * rX)) / denom;
        if (t < 0 || t > 1 || u < 0 || u > 1) return null;

        return { yx: [ay + t * rY, ax + t * rX], t: t, u: u };
    }

    function buildLaneGraph(lanesInput) {
        var nodes = []; // [y,x]
        var edges = new Map(); // id -> Array<{to, w}>

        // Merge nearby nodes so paths can cross even if the JSON isn't perfectly snapped.
        var mergeDistance = Math.max(6, Math.min(24, lanesSnapDistance * 0.18)); // pixels
        var grid = new Map(); // "gx,gy" -> Array<nodeId>

        function bucketKey(gx, gy) { return gx + ',' + gy; }
        function bucketCoord(pt) {
            var gx = Math.floor(pt[1] / mergeDistance);
            var gy = Math.floor(pt[0] / mergeDistance);
            return { gx: gx, gy: gy };
        }

        function addNode(pt) {
            var bc = bucketCoord(pt);
            for (var dy = -1; dy <= 1; dy++) {
                for (var dx = -1; dx <= 1; dx++) {
                    var key = bucketKey(bc.gx + dx, bc.gy + dy);
                    var ids = grid.get(key);
                    if (!ids) continue;
                    for (var i = 0; i < ids.length; i++) {
                        var id = ids[i];
                        if (dist(nodes[id], pt) <= mergeDistance) return id;
                    }
                }
            }

            var newId = nodes.length;
            nodes.push(pt);
            edges.set(newId, []);
            var k = bucketKey(bc.gx, bc.gy);
            if (!grid.has(k)) grid.set(k, []);
            grid.get(k).push(newId);
            return newId;
        }

        function upsertEdge(from, to, w) {
            var adj = edges.get(from);
            for (var i = 0; i < adj.length; i++) {
                if (adj[i].to === to) {
                    if (w < adj[i].w) adj[i].w = w;
                    return;
                }
            }
            adj.push({ to: to, w: w });
        }

        function addUndirectedEdge(aId, bId) {
            if (aId === bId) return;
            var w = dist(nodes[aId], nodes[bId]);
            upsertEdge(aId, bId, w);
            upsertEdge(bId, aId, w);
        }

        // Build segments (for intersection splitting).
        var segments = [];
        (lanesInput || []).forEach(function (lane) {
            if (!lane || !Array.isArray(lane.points)) return;
            var pts = lane.points
                .map(function (p) { return [Number(p.y), Number(p.x)]; })
                .filter(function (p) { return isFinite(p[0]) && isFinite(p[1]); });
            for (var i = 0; i < pts.length - 1; i++) {
                segments.push({ a: pts[i], b: pts[i + 1] });
            }
        });

        var splits = new Array(segments.length);
        for (var s = 0; s < segments.length; s++) {
            splits[s] = [
                { t: 0, yx: segments[s].a },
                { t: 1, yx: segments[s].b },
            ];
        }

        function bboxOverlap(sa, sb) {
            var aMinY = Math.min(sa.a[0], sa.b[0]);
            var aMaxY = Math.max(sa.a[0], sa.b[0]);
            var aMinX = Math.min(sa.a[1], sa.b[1]);
            var aMaxX = Math.max(sa.a[1], sa.b[1]);
            var bMinY = Math.min(sb.a[0], sb.b[0]);
            var bMaxY = Math.max(sb.a[0], sb.b[0]);
            var bMinX = Math.min(sb.a[1], sb.b[1]);
            var bMaxX = Math.max(sb.a[1], sb.b[1]);
            return !(aMaxX < bMinX || bMaxX < aMinX || aMaxY < bMinY || bMaxY < aMinY);
        }

        // Split segments where they cross (so Dijkstra can turn at intersections).
        for (var iSeg = 0; iSeg < segments.length; iSeg++) {
            for (var jSeg = iSeg + 1; jSeg < segments.length; jSeg++) {
                var s1 = segments[iSeg];
                var s2 = segments[jSeg];
                if (!bboxOverlap(s1, s2)) continue;

                var hit = segmentIntersection(s1.a, s1.b, s2.a, s2.b);
                if (!hit) continue;

                // De-dupe segment endpoints (they'll merge via addNode anyway).
                splits[iSeg].push({ t: hit.t, yx: hit.yx });
                splits[jSeg].push({ t: hit.u, yx: hit.yx });
            }
        }

        function uniqueSortedSplitPoints(splitList) {
            var epsT = 1e-6;
            splitList.sort(function (p, q) { return p.t - q.t; });
            var out = [];
            for (var i = 0; i < splitList.length; i++) {
                if (!out.length) { out.push(splitList[i]); continue; }
                if (Math.abs(splitList[i].t - out[out.length - 1].t) <= epsT) continue;
                out.push(splitList[i]);
            }
            return out;
        }

        for (var segIdx = 0; segIdx < segments.length; segIdx++) {
            var ptsOnSeg = uniqueSortedSplitPoints(splits[segIdx]);
            if (ptsOnSeg.length < 2) continue;
            var prevId = null;
            for (var k = 0; k < ptsOnSeg.length; k++) {
                var id = addNode(ptsOnSeg[k].yx);
                if (prevId !== null) addUndirectedEdge(prevId, id);
                prevId = id;
            }
        }

        return { nodes: nodes, edges: edges };
    }

    function snapPointToGraph(point, graph) {
        // point: [y,x]
        var best = { d: Infinity, yx: null, aId: null, bId: null };
        graph.edges.forEach(function (adj, aId) {
            adj.forEach(function (e) {
                var bId = e.to;
                if (bId < aId) return; // de-dupe undirected segments
                var a = graph.nodes[aId];
                var b = graph.nodes[bId];
                var c = closestPointOnSegment(point, a, b);
                var yx = [c[0], c[1]];
                var d = dist(point, yx);
                if (d < best.d) best = { d: d, yx: yx, aId: aId, bId: bId };
            });
        });

        if (!isFinite(best.d) || best.d > lanesSnapDistance || !best.yx) return null;
        return best;
    }

    function removeUndirectedEdge(graph, aId, bId) {
        var adjA = graph.edges.get(aId) || [];
        var adjB = graph.edges.get(bId) || [];
        graph.edges.set(aId, adjA.filter(function (e) { return e.to !== bId; }));
        graph.edges.set(bId, adjB.filter(function (e) { return e.to !== aId; }));
    }

    function addUndirectedEdgeToGraph(graph, aId, bId) {
        if (aId === bId) return;
        var w = dist(graph.nodes[aId], graph.nodes[bId]);
        var adjA = graph.edges.get(aId) || [];
        var adjB = graph.edges.get(bId) || [];

        function upsert(adj, to, wVal) {
            for (var i = 0; i < adj.length; i++) {
                if (adj[i].to === to) { if (wVal < adj[i].w) adj[i].w = wVal; return; }
            }
            adj.push({ to: to, w: wVal });
        }

        upsert(adjA, bId, w);
        upsert(adjB, aId, w);
        graph.edges.set(aId, adjA);
        graph.edges.set(bId, adjB);
    }

    function insertSnapNode(graph, snap) {
        if (!snap) return null;
        var a = graph.nodes[snap.aId];
        var b = graph.nodes[snap.bId];
        if (dist(snap.yx, a) < 0.75) return snap.aId;
        if (dist(snap.yx, b) < 0.75) return snap.bId;

        var newId = graph.nodes.length;
        graph.nodes.push(snap.yx);
        graph.edges.set(newId, []);

        // Split the snapped segment so routes "stay on the walkway".
        removeUndirectedEdge(graph, snap.aId, snap.bId);
        addUndirectedEdgeToGraph(graph, snap.aId, newId);
        addUndirectedEdgeToGraph(graph, newId, snap.bId);
        return newId;
    }

    function dijkstra(graph, startId, goalId) {
        var n = graph.nodes.length;
        var distArr = new Array(n).fill(Infinity);
        var prev = new Array(n).fill(null);
        var visited = new Array(n).fill(false);
        distArr[startId] = 0;

        for (;;) {
            var u = -1;
            var best = Infinity;
            for (var i = 0; i < n; i++) {
                if (visited[i]) continue;
                if (distArr[i] < best) { best = distArr[i]; u = i; }
            }
            if (u === -1) break;
            if (u === goalId) break;
            visited[u] = true;

            var adj = graph.edges.get(u) || [];
            for (var k = 0; k < adj.length; k++) {
                var v = adj[k].to;
                var w = adj[k].w;
                if (visited[v]) continue;
                var alt = distArr[u] + w;
                if (alt < distArr[v]) {
                    distArr[v] = alt;
                    prev[v] = u;
                }
            }
        }

        if (!isFinite(distArr[goalId])) return null;
        var path = [];
        var cur = goalId;
        while (cur !== null) {
            path.push(cur);
            if (cur === startId) break;
            cur = prev[cur];
        }
        path.reverse();
        return path;
    }

    loadImageDimensions(imageUrl, function (dim) {
        var map = L.map('locatorMap', {
            crs: L.CRS.Simple,
            minZoom: -2,
            zoomSnap: 0.25,
            zoomDelta: 0.25,
            attributionControl: false,
        });

        var bounds = [[0, 0], [dim.height, dim.width]];
        L.imageOverlay(imageUrl, bounds).addTo(map);
        map.fitBounds(bounds);

        var entranceLatLng = L.latLng(Number(entrance.y || 0), Number(entrance.x || 0));
        // Entrance marker hidden - only show lot

        var lotLatLng = L.latLng(Number(lot.latitude || 0), Number(lot.longitude || 0));

        var highlightStyle = {
            color: '#dc3545',
            weight: 3,
            opacity: 0.95,
            fillColor: '#dc3545',
            fillOpacity: 0.20,
        };

        var lotLayer = null;
        if (lot.geometry_type === 'rect' && lot.geometry) {
            var x = Number(lot.geometry.x || 0);
            var y = Number(lot.geometry.y || 0);
            var w = Number(lot.geometry.w || 0);
            var h = Number(lot.geometry.h || 0);
            lotLayer = L.rectangle([[y, x], [y + h, x + w]], highlightStyle).addTo(map);
            lotLatLng = L.latLng(y + h / 2, x + w / 2);
        } else if (lot.geometry_type === 'poly' && lot.geometry && Array.isArray(lot.geometry.points)) {
            var pts = lot.geometry.points
                .map(function (p) { return [Number(p.y || 0), Number(p.x || 0)]; })
                .filter(function (p) { return isFinite(p[0]) && isFinite(p[1]); });
            if (pts.length >= 3) {
                lotLayer = L.polygon(pts, highlightStyle).addTo(map);
                var sumY = 0, sumX = 0;
                pts.forEach(function (p) { sumY += p[0]; sumX += p[1]; });
                lotLatLng = L.latLng(sumY / pts.length, sumX / pts.length);
            }
        }

        L.marker(lotLatLng, { icon: lotIcon() }).addTo(map);

        var focus = lotLayer && lotLayer.getBounds ? lotLayer.getBounds().pad(0.6) : L.latLngBounds([lotLatLng, lotLatLng]).pad(0.6);
        map.fitBounds(focus, { animate: true });
    });
});
</script>
@endsection
