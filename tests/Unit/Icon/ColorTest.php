<?php

namespace Tests\Unit\Icon;

use App\Icon\Color;
use PHPUnit\Framework\TestCase;

class ColorTest extends TestCase
{
    public function test_can_get_standard_colors()
    {
        $colors = Color::getStandardColors();
        
        $this->assertIsArray($colors);
        $this->assertCount(17, $colors); // 16 預設調色盤 + 1 系統主色
        $this->assertContains('#9b6eff', $colors);
        $this->assertContains('#ef4444', $colors);
    }
    
    public function test_can_get_light_colors()
    {
        $colors = Color::getLightColors();
        
        $this->assertIsArray($colors);
        $this->assertCount(17, $colors); // 包含 purple-50
        $this->assertContains('#fecaca', $colors);
        $this->assertContains('#ddd6fe', $colors);
        $this->assertContains('#faf5ff', $colors); // purple-50
    }
    
    public function test_can_get_light_color_with_dark_foreground()
    {
        $lightColors = Color::getLightColorsWithForeground();
        
        $this->assertIsArray($lightColors);
        $this->assertCount(17, $lightColors); // 包含 purple-50
        
        // 檢查第一個淡紅色組合
        $this->assertArrayHasKey('#fecaca', $lightColors);
        $this->assertEquals('#991b1b', $lightColors['#fecaca']);
        
        // 檢查淡紫色組合
        $this->assertArrayHasKey('#e9d5ff', $lightColors);
        $this->assertEquals('#581c87', $lightColors['#e9d5ff']);
    }
    
    public function test_can_get_gray_colors()
    {
        $colors = Color::getGrayColors();
        
        $this->assertIsArray($colors);
        $this->assertCount(11, $colors); // 黑白 + 9 個灰階
        $this->assertContains('#000000', $colors);
        $this->assertContains('#ffffff', $colors);
        $this->assertContains('#6b7280', $colors);
    }
    
    public function test_can_validate_allowed_background_color()
    {
        // 標準色
        $this->assertTrue(Color::isAllowedBackgroundColor('#9b6eff'));
        $this->assertTrue(Color::isAllowedBackgroundColor('#ef4444'));
        
        // 淡色系
        $this->assertTrue(Color::isAllowedBackgroundColor('#fecaca'));
        $this->assertTrue(Color::isAllowedBackgroundColor('#ddd6fe'));
        
        // 灰階
        $this->assertTrue(Color::isAllowedBackgroundColor('#f3f4f6'));
        
        // 不允許的顏色
        $this->assertFalse(Color::isAllowedBackgroundColor('#123456'));
        $this->assertFalse(Color::isAllowedBackgroundColor('invalid'));
    }
    
    public function test_can_calculate_luminance()
    {
        // 白色應該有最高亮度
        $this->assertGreaterThan(0.9, Color::getLuminance('#ffffff'));
        
        // 黑色應該有最低亮度
        $this->assertLessThan(0.1, Color::getLuminance('#000000'));
        
        // 中間色
        $luminance = Color::getLuminance('#9b6eff');
        $this->assertGreaterThan(0.3, $luminance);
        $this->assertLessThan(0.7, $luminance);
    }
    
    public function test_can_get_contrast_color()
    {
        // 亮背景應該返回深色文字
        $this->assertEquals('#1f2937', Color::getContrastColor('#ffffff'));
        $this->assertEquals('#1f2937', Color::getContrastColor('#fecaca'));
        
        // 暗背景應該返回白色文字
        $this->assertEquals('#ffffff', Color::getContrastColor('#000000'));
        $this->assertEquals('#ffffff', Color::getContrastColor('#9b6eff'));
    }
    
    public function test_can_generate_random_standard_color()
    {
        $color = Color::randomStandardColor();
        
        $this->assertMatchesRegularExpression('/^#[0-9a-f]{6}$/i', $color);
        $this->assertTrue(Color::isAllowedBackgroundColor($color));
        $this->assertContains($color, Color::getStandardColors());
    }
    
    public function test_can_generate_random_light_color()
    {
        $color = Color::randomLightColor();
        
        $this->assertMatchesRegularExpression('/^#[0-9a-f]{6}$/i', $color);
        $this->assertTrue(Color::isAllowedBackgroundColor($color));
        $this->assertContains($color, Color::getLightColors());
    }
    
    public function test_can_generate_random_light_color_with_foreground()
    {
        $colorPair = Color::randomLightColorWithForeground();
        
        $this->assertIsArray($colorPair);
        $this->assertArrayHasKey('background', $colorPair);
        $this->assertArrayHasKey('foreground', $colorPair);
        
        $this->assertTrue(Color::isAllowedBackgroundColor($colorPair['background']));
        $this->assertContains($colorPair['background'], Color::getLightColors());
        
        // 確認前景色是對應的深色
        $lightColors = Color::getLightColorsWithForeground();
        $this->assertEquals($lightColors[$colorPair['background']], $colorPair['foreground']);
    }
    
    public function test_can_validate_hex_color()
    {
        $this->assertTrue(Color::isValidHexColor('#9b6eff'));
        $this->assertTrue(Color::isValidHexColor('#000000'));
        $this->assertTrue(Color::isValidHexColor('#FFFFFF'));
        
        $this->assertFalse(Color::isValidHexColor('9b6eff')); // 缺少 #
        $this->assertFalse(Color::isValidHexColor('#9b6ef')); // 只有 5 位
        $this->assertFalse(Color::isValidHexColor('#gggggg')); // 無效字符
        $this->assertFalse(Color::isValidHexColor('invalid'));
    }
    
    public function test_can_normalize_hex_color()
    {
        $this->assertEquals('#9b6eff', Color::normalizeHexColor('#9B6EFF'));
        $this->assertEquals('#9b6eff', Color::normalizeHexColor('9b6eff'));
        $this->assertEquals('#9b6eff', Color::normalizeHexColor('9B6EFF'));
        
        $this->assertNull(Color::normalizeHexColor('invalid'));
    }
    
    public function test_can_get_luminance_threshold()
    {
        // 測試取得亮度閾值
        $this->assertEquals(0.6, Color::getLuminanceThreshold());
    }
    
    public function test_can_set_luminance_threshold()
    {
        // 測試設定亮度閾值
        $originalThreshold = Color::getLuminanceThreshold();
        
        Color::setLuminanceThreshold(0.5);
        $this->assertEquals(0.5, Color::getLuminanceThreshold());
        
        // 恢復原始值
        Color::setLuminanceThreshold($originalThreshold);
    }
    
    public function test_can_get_foreground_colors()
    {
        // 測試取得前景色設定
        $this->assertEquals('#1f2937', Color::getDarkForegroundColor());
        $this->assertEquals('#ffffff', Color::getLightForegroundColor());
    }
    
    public function test_can_set_foreground_colors()
    {
        // 測試設定前景色
        $originalDark = Color::getDarkForegroundColor();
        $originalLight = Color::getLightForegroundColor();
        
        Color::setDarkForegroundColor('#000000');
        Color::setLightForegroundColor('#f0f0f0');
        
        $this->assertEquals('#000000', Color::getDarkForegroundColor());
        $this->assertEquals('#f0f0f0', Color::getLightForegroundColor());
        
        // 恢復原始值
        Color::setDarkForegroundColor($originalDark);
        Color::setLightForegroundColor($originalLight);
    }
    
    public function test_can_validate_color_combination()
    {
        // 測試驗證顏色組合
        
        // 標準深色背景 + 白色前景（有效）
        $this->assertTrue(Color::validateColorCombination('#9b6eff', '#ffffff'));
        
        // 標準深色背景 + 深色前景（無效）
        $this->assertFalse(Color::validateColorCombination('#9b6eff', '#1f2937'));
        
        // 預設淡色背景 + 對應深色前景（有效）
        $this->assertTrue(Color::validateColorCombination('#fecaca', '#991b1b'));
        $this->assertTrue(Color::validateColorCombination('#faf5ff', '#7c3aed'));
        
        // 預設淡色背景 + 錯誤深色前景（無效）
        $this->assertFalse(Color::validateColorCombination('#fecaca', '#7c3aed'));
        
        // 預設淡色背景 + 白色前景（無效）
        $this->assertFalse(Color::validateColorCombination('#fecaca', '#ffffff'));
        
        // 非預設顏色 + 根據亮度自動判斷
        // 亮背景（例如白色）+ 深色前景（有效）
        $this->assertTrue(Color::validateColorCombination('#f8f8f8', '#1f2937'));
        
        // 亮背景 + 白色前景（無效）
        $this->assertFalse(Color::validateColorCombination('#f8f8f8', '#ffffff'));
        
        // 暗背景（例如深灰）+ 白色前景（有效）
        $this->assertTrue(Color::validateColorCombination('#333333', '#ffffff'));
        
        // 暗背景 + 深色前景（無效）
        $this->assertFalse(Color::validateColorCombination('#333333', '#1f2937'));
    }
    
    public function test_can_generate_random_color_combination()
    {
        // 測試隨機生成顏色組合（預設模式）
        $combination = Color::randomColorCombination();
        
        $this->assertIsArray($combination);
        $this->assertArrayHasKey('background', $combination);
        $this->assertArrayHasKey('foreground', $combination);
        
        // 驗證生成的組合是有效的
        $this->assertTrue(Color::validateColorCombination(
            $combination['background'],
            $combination['foreground']
        ));
        
        // 確認背景色來自預設顏色
        $this->assertTrue(Color::isAllowedBackgroundColor($combination['background']));
    }
    
    public function test_can_generate_completely_random_color_combination()
    {
        // 測試完全隨機生成顏色組合
        $combination = Color::randomColorCombination(true);
        
        $this->assertIsArray($combination);
        $this->assertArrayHasKey('background', $combination);
        $this->assertArrayHasKey('foreground', $combination);
        
        // 驗證是有效的 hex 顏色
        $this->assertTrue(Color::isValidHexColor($combination['background']));
        $this->assertTrue(Color::isValidHexColor($combination['foreground']));
        
        // 驗證前景色是允許的（白色或深灰色）
        $this->assertContains($combination['foreground'], [
            Color::getLightForegroundColor(),
            Color::getDarkForegroundColor()
        ]);
        
        // 驗證組合的對比度是足夠的
        $backgroundLuminance = Color::getLuminance($combination['background']);
        $threshold = Color::getLuminanceThreshold();
        
        if ($backgroundLuminance > $threshold) {
            $this->assertEquals(Color::getDarkForegroundColor(), $combination['foreground']);
        } else {
            $this->assertEquals(Color::getLightForegroundColor(), $combination['foreground']);
        }
    }
    
    public function test_random_color_combination_respects_predefined_pairs()
    {
        // 測試隨機生成時如果選到淡色背景，會使用對應的深色前景
        $found = false;
        
        // 多次測試以確保能覆蓋到淡色背景的情況
        for ($i = 0; $i < 50; $i++) {
            $combination = Color::randomColorCombination();
            
            if (array_key_exists($combination['background'], Color::getLightColorsWithForeground())) {
                $found = true;
                $expectedForeground = Color::getLightColorsWithForeground()[$combination['background']];
                $this->assertEquals($expectedForeground, $combination['foreground']);
                break;
            }
        }
        
        // 至少應該有一次生成淡色背景
        if (!$found) {
            $this->markTestSkipped('未能生成淡色背景組合，可能需要調整隨機算法');
        }
    }
}