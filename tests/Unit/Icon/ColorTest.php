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
}