<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Place') }}
        </h2>
    </x-slot>
    <style>
        #map {
            width: 100%;
            height: 500px;
            /* 必要に応じて調整 */
        }
    </style>

    <body>
        <div id="map"></div>
        <div id="supermarket" class="ml-2">
            <h1>スーパーマーケット一覧</h1>
        </div>
    </body>
    <script>
        (g => {
            var h, a, k, p = "The Google Maps JavaScript API",
                c = "google",
                l = "importLibrary",
                q = "__ib__",
                m = document,
                b = window;
            b = b[c] || (b[c] = {});
            var d = b.maps || (b.maps = {}),
                r = new Set,
                e = new URLSearchParams,
                u = () => h || (h = new Promise(async (f, n) => {
                    await (a = m.createElement("script"));
                    e.set("libraries", [...r] + "");
                    for (k in g) e.set(k.replace(/[A-Z]/g, t => "_" + t[0].toLowerCase()), g[k]);
                    e.set("callback", c + ".maps." + q);
                    a.src = `https://maps.${c}apis.com/maps/api/js?` + e;
                    d[q] = f;
                    a.onerror = () => h = n(Error(p + " could not load."));
                    a.nonce = m.querySelector("script[nonce]")?.nonce || "";
                    m.head.append(a)
                }));
            d[l] ? console.warn(p + " only loads once. Ignoring:", g) : d[l] = (f, ...n) => r.add(f) && u().then(() => d[l](f, ...n))
        })({
            key: "AIzaSyBNL6rm8t - y2GL6U3cRbrNTlm249zW0Uyw",
            v: "weekly",
            // Use the 'v' parameter to indicate the version to use (weekly, beta, alpha, etc.).
            // Add other bootstrap parameters as needed, using camel case.
        });
    </script>
    <script>
        let map, infoWindow;
        async function initMap() {
            const {
                Map,
                InfoWindow
            } = await google.maps.importLibrary("maps");
            const {
                Place,
                SearchNearbyRankPreference
            } = await google.maps.importLibrary("places");
            const {
                AdvancedMarkerElement
            } = await google.maps.importLibrary("marker");
            infoWindow = new InfoWindow();
            // 現在地を取得してマップの中心に設定
            if (navigator.geolocation) {
                navigator.geolocation.getCurrentPosition(
                    (position) => {
                        const userLocation = {
                            lat: position.coords.latitude,
                            lng: position.coords.longitude,
                        };
                        // 地図を現在地に設定
                        map = new Map(document.getElementById("map"), {
                            center: userLocation,
                            zoom: 14,
                            mapId: "DEMO_MAP_ID",
                        });
                        // 現在地のマーカーを追加
                        const userMarker = new AdvancedMarkerElement({
                            map,
                            position: userLocation,
                            title: "あなたの現在地",
                        });
                        infoWindow.setPosition(userLocation);
                        infoWindow.setContent("現在地");
                        infoWindow.open(map);
                        // 近くのスーパーマーケットを取得
                        fetchSupermarkets(userLocation);
                    },
                    () => {
                        handleLocationError(true, infoWindow, {
                            lat: 35.6895,
                            lng: 139.6917
                        }); // 失敗時は東京をデフォルトに
                    }
                );
            } else {
                handleLocationError(false, infoWindow, {
                    lat: 35.6895,
                    lng: 139.6917
                });
            }
            // // 現在地ボタンを追加
            // const locationButton = document.createElement("button");
            // locationButton.textContent = "現在地へ移動";
            // locationButton.classList.add("custom-map-control-button");
            // document.getElementById("map").appendChild(locationButton);
            // locationButton.addEventListener("click", () => {
            //     if (navigator.geolocation) {
            //         navigator.geolocation.getCurrentPosition(
            //             (position) => {
            //                 const userLocation = {
            //                     lat: position.coords.latitude,
            //                     lng: position.coords.longitude,
            //                 };
            //                 map.setCenter(userLocation);
            //                 map.setZoom(14);
            //                 infoWindow.setPosition(userLocation);
            //                 infoWindow.setContent("現在地");
            //                 infoWindow.open(map);
            //                 fetchSupermarkets(userLocation);
            //             },
            //             () => {
            //                 handleLocationError(true, infoWindow, map.getCenter());
            //             }
            //         );
            //     } else {
            //         handleLocationError(false, infoWindow, map.getCenter());
            //     }
            // });
        }
        // エラー処理
        function handleLocationError(browserHasGeolocation, infoWindow, pos) {
            map = new google.maps.Map(document.getElementById("map"), {
                center: pos, // デフォルト位置（東京）
                zoom: 12,
            });
            infoWindow.setPosition(pos);
            infoWindow.setContent(
                browserHasGeolocation ?
                "エラー: 位置情報サービスが失敗しました。" :
                "エラー: お使いのブラウザは位置情報をサポートしていません。"
            );
            infoWindow.open(map);
        }
        // 近くのスーパーマーケットを取得
        async function fetchSupermarkets(location) {
            const {
                Place,
                SearchNearbyRankPreference
            } = await google.maps.importLibrary("places");
            const {
                AdvancedMarkerElement,
                PinElement
            } = await google.maps.importLibrary("marker");
            const request = {
                fields: ["displayName", "location", "businessStatus", "websiteURI"],
                locationRestriction: {
                    center: location,
                    radius: 5000, // 半径5km以内
                },
                includedPrimaryTypes: ["supermarket"],
                maxResultCount: 10,
                rankPreference: SearchNearbyRankPreference.POPULARITY,
                language: "ja",
            };
            //@ts-ignore
            const {
                places
            } = await Place.searchNearby(request);
            if (places.length) {
                console.log("取得したスーパーマーケット:", places);
                const {
                    LatLngBounds
                } = await google.maps.importLibrary("core");
                const bounds = new LatLngBounds();
                const listSupermarkets = document.createElement("ul");
                places.forEach((place) => {
                    const listSupermarket = document.createElement("li");
                    const listSupermarketURI = document.createElement("a");
                    listSupermarketURI.textContent = place.displayName;
                    listSupermarketURI.href = place.Eg.websiteURI;
                    listSupermarket.appendChild(listSupermarketURI);
                    listSupermarkets.appendChild(listSupermarket);
                    // マーカーの色を変更
                    const pinBackground = new PinElement({
                        background: "#34A853",
                    });
                    const markerView = new AdvancedMarkerElement({
                        map,
                        position: place.location,
                        title: place.displayName,
                        content: pinBackground.element,
                    });
                    const infoWindow = new google.maps.InfoWindow({
                        content: `<strong>${place.displayName}</strong>`,
                    });
                    markerView.addListener("click", () => {
                        infoWindow.open(map, markerView);
                    });
                    bounds.extend(place.location);
                });
                map.fitBounds(bounds);
                document.getElementById("supermarket").after(listSupermarkets);
                listSupermarkets.style.marginLeft = "20px";
            } else {
                console.log("近くにスーパーマーケットがありません");
            }
        }
        initMap();
    </script>
</x-app-layout>