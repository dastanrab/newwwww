'use client';
import { useEffect, useRef } from 'react';
import Map from '@neshan-maps-platform/ol/Map';
import View from '@neshan-maps-platform/ol/View';
import { fromLonLat, toLonLat } from '@neshan-maps-platform/ol/proj';

import Feature from '@neshan-maps-platform/ol/Feature';
import Point from '@neshan-maps-platform/ol/geom/Point';
import VectorLayer from '@neshan-maps-platform/ol/layer/Vector';
import VectorSource from '@neshan-maps-platform/ol/source/Vector';
import Style from '@neshan-maps-platform/ol/style/Style';
import Icon from '@neshan-maps-platform/ol/style/Icon';

interface SubmitAdressMapProps {
    onSelect?: (coords: { lat: number; lon: number }) => void;
}

export default function SubmitAdressMap({ onSelect }: SubmitAdressMapProps) {
    const mapRef = useRef<HTMLDivElement | null>(null);
    const mapInstance = useRef<Map | null>(null);
    const vectorSourceRef = useRef<VectorSource | null>(null);

    // ✅ نگه داشتن onSelect بدون ایجاد re-render
    const onSelectRef = useRef(onSelect);

    useEffect(() => {
        onSelectRef.current = onSelect;
    }, [onSelect]);

    useEffect(() => {
        if (!mapRef.current || mapInstance.current) return;

        const vectorSource = new VectorSource();
        vectorSourceRef.current = vectorSource;

        const vectorLayer = new VectorLayer({
            source: vectorSource,
        });

        const map = new Map({
            mapType: 'neshan',
            target: mapRef.current,
            key: 'web.7f11b5c6971d4917a6e9272a522d8b9e',
            poi: true,
            traffic: true,
            layers: [vectorLayer],
            view: new View({
                center: fromLonLat([ 59.58874,36.28865]),
                zoom: 14,
            }),
        });

        // ✅ فقط یک بار bind میشه
        map.on('singleclick', (event) => {
            const [lon, lat] = toLonLat(event.coordinate);

            vectorSource.clear();

            const marker = new Feature({
                geometry: new Point(fromLonLat([lon, lat])),
            });

            marker.setStyle(
                new Style({
                    image: new Icon({
                        src: '/favicon.png',
                        anchor: [0.5, 1],
                        scale: 0.05,
                    }),
                })
            );

            vectorSource.addFeature(marker);

            // 👇 استفاده از ref به جای prop مستقیم
            onSelectRef.current?.({ lat, lon });
        });

        mapInstance.current = map;

    }, []);

    return (
        <div
            ref={mapRef}
            style={{
                width: '100%',
                height: '100%',
                borderRadius: 12,
                overflow: 'hidden',
            }}
        />
    );
}