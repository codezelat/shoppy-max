            // 1. Revert Stock for OLD items
            $variantQuantities = [];
            foreach ($order->items as $item) {
                if ($item->product_variant_id) {
                    $variantId = $item->product_variant_id;
                    if (!isset($variantQuantities[$variantId])) {
                        $variantQuantities[$variantId] = 0;
                    }
                    $variantQuantities[$variantId] += $item->quantity;
                }
            }

            if (!empty($variantQuantities)) {
                $variants = ProductVariant::without('unit')->whereIn('id', array_keys($variantQuantities))->get();
                foreach ($variants as $variant) {
                    if (isset($variantQuantities[$variant->id])) {
                        $variant->increment('quantity', $variantQuantities[$variant->id]);
                    }
                }
            }
