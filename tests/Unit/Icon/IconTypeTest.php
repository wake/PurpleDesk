<?php

namespace Tests\Unit\Icon;

use App\Icon\IconTypeInterface;
use App\Icon\Types\TextIcon;
use App\Icon\Types\EmojiIcon;
use App\Icon\Types\HeroIcon;
use App\Icon\Types\BootstrapIcon;
use App\Icon\Types\ImageIcon;
use Tests\TestCase;

class IconTypeTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        
        // 載入圖標配置
        config()->set('icon', [
            'colors' => require base_path('config/icon/colors.php'),
            'heroicons' => require base_path('config/icon/heroicons.php'),
            'bootstrap_icons' => require base_path('config/icon/bootstrap_icons.php'),
        ]);
    }
    public function test_text_icon_validates_correctly()
    {
        $textIcon = new TextIcon();
        
        // 有效的文字圖標
        $this->assertTrue($textIcon->validate([
            'type' => 'text',
            'text' => 'AB',
            'backgroundColor' => '#6366f1',
            'textColor' => '#ffffff'
        ]));
        
        // 中文字符
        $this->assertTrue($textIcon->validate([
            'type' => 'text',
            'text' => '測試',
            'backgroundColor' => '#6366f1',
            'textColor' => '#1f2937'
        ]));
        
        // 缺少必要欄位
        $this->assertFalse($textIcon->validate([
            'type' => 'text',
            'text' => 'AB'
            // 缺少 backgroundColor 和 textColor
        ]));
        
        // 文字太長
        $this->assertFalse($textIcon->validate([
            'type' => 'text',
            'text' => 'ABC', // 超過 2 個字符
            'backgroundColor' => '#6366f1',
            'textColor' => '#ffffff'
        ]));
        
        // 無效的顏色
        $this->assertFalse($textIcon->validate([
            'type' => 'text',
            'text' => 'AB',
            'backgroundColor' => 'invalid',
            'textColor' => '#ffffff'
        ]));
    }
    
    public function test_text_icon_generates_random()
    {
        $textIcon = new TextIcon();
        $data = $textIcon->generateRandom();
        
        $this->assertIsArray($data);
        $this->assertEquals('text', $data['type']);
        $this->assertArrayHasKey('text', $data);
        $this->assertArrayHasKey('backgroundColor', $data);
        $this->assertArrayHasKey('textColor', $data);
        
        // 驗證生成的資料是有效的
        $this->assertTrue($textIcon->validate($data));
    }
    
    public function test_emoji_icon_validates_correctly()
    {
        $emojiIcon = new EmojiIcon();
        
        // 有效的 emoji
        $this->assertTrue($emojiIcon->validate([
            'type' => 'emoji',
            'emoji' => '😀',
            'backgroundColor' => '#fef2f2' // red-50
        ]));
        
        // 缺少 emoji
        $this->assertFalse($emojiIcon->validate([
            'type' => 'emoji',
            'backgroundColor' => '#fef2f2' // red-50
        ]));
        
        // 無效的背景色
        $this->assertFalse($emojiIcon->validate([
            'type' => 'emoji',
            'emoji' => '😀',
            'backgroundColor' => '#123456'
        ]));
    }
    
    public function test_emoji_icon_generates_random()
    {
        $emojiIcon = new EmojiIcon();
        $data = $emojiIcon->generateRandom();
        
        $this->assertIsArray($data);
        $this->assertEquals('emoji', $data['type']);
        $this->assertArrayHasKey('emoji', $data);
        $this->assertArrayHasKey('backgroundColor', $data);
        
        $this->assertTrue($emojiIcon->validate($data));
    }
    
    public function test_hero_icon_validates_correctly()
    {
        $heroIcon = new HeroIcon();
        
        // 有效的 hero icon
        $this->assertTrue($heroIcon->validate([
            'type' => 'hero_icon',
            'icon' => 'office-building',
            'style' => 'outline',
            'backgroundColor' => '#faf5ff',
            'iconColor' => '#7c3aed'
        ]));
        
        // solid 樣式
        $this->assertTrue($heroIcon->validate([
            'type' => 'hero_icon',
            'icon' => 'user',
            'style' => 'solid',
            'backgroundColor' => '#faf5ff',
            'iconColor' => '#ffffff'
        ]));
        
        // 無效的樣式
        $this->assertFalse($heroIcon->validate([
            'type' => 'hero_icon',
            'icon' => 'office-building',
            'style' => 'invalid',
            'backgroundColor' => '#faf5ff',
            'iconColor' => '#7c3aed'
        ]));
        
        // 缺少必要欄位
        $this->assertFalse($heroIcon->validate([
            'type' => 'hero_icon',
            'icon' => 'office-building'
        ]));
    }
    
    public function test_hero_icon_generates_random()
    {
        $heroIcon = new HeroIcon();
        $data = $heroIcon->generateRandom();
        
        $this->assertIsArray($data);
        $this->assertEquals('hero_icon', $data['type']);
        $this->assertArrayHasKey('icon', $data);
        $this->assertArrayHasKey('style', $data);
        $this->assertArrayHasKey('backgroundColor', $data);
        $this->assertArrayHasKey('iconColor', $data);
        $this->assertContains($data['style'], ['outline', 'solid']);
        
        $this->assertTrue($heroIcon->validate($data));
    }
    
    public function test_bootstrap_icon_validates_correctly()
    {
        $bsIcon = new BootstrapIcon();
        
        // 有效的 bootstrap icon
        $this->assertTrue($bsIcon->validate([
            'type' => 'bootstrap_icon',
            'icon' => 'bi-people',
            'style' => 'outline',
            'backgroundColor' => '#eff6ff', // blue-50
            'iconColor' => '#2563eb'
        ]));
        
        // fill 樣式
        $this->assertTrue($bsIcon->validate([
            'type' => 'bootstrap_icon',
            'icon' => 'bi-person-fill',
            'style' => 'fill',
            'backgroundColor' => '#eff6ff', // blue-50
            'iconColor' => '#2563eb'
        ]));
        
        // 無效的樣式
        $this->assertFalse($bsIcon->validate([
            'type' => 'bootstrap_icon',
            'icon' => 'bi-people',
            'style' => 'solid', // 應該是 outline 或 fill
            'backgroundColor' => '#eff6ff', // blue-50
            'iconColor' => '#2563eb'
        ]));
    }
    
    public function test_bootstrap_icon_generates_random()
    {
        $bsIcon = new BootstrapIcon();
        $data = $bsIcon->generateRandom();
        
        $this->assertIsArray($data);
        $this->assertEquals('bootstrap_icon', $data['type']);
        $this->assertArrayHasKey('icon', $data);
        $this->assertArrayHasKey('style', $data);
        $this->assertArrayHasKey('backgroundColor', $data);
        $this->assertArrayHasKey('iconColor', $data);
        $this->assertContains($data['style'], ['outline', 'fill']);
        
        $this->assertTrue($bsIcon->validate($data));
    }
    
    public function test_image_icon_validates_correctly()
    {
        $imageIcon = new ImageIcon();
        
        // 有效的圖片圖標
        $this->assertTrue($imageIcon->validate([
            'type' => 'image',
            'path' => '/storage/avatars/user123.jpg'
        ]));
        
        // base64 圖片
        $this->assertTrue($imageIcon->validate([
            'type' => 'image',
            'path' => 'data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAYAAAAfFcSJAAAADUlEQVR42mNkYPhfDwAChwGA60e6kgAAAABJRU5ErkJggg=='
        ]));
        
        // 缺少 path
        $this->assertFalse($imageIcon->validate([
            'type' => 'image'
        ]));
        
        // 空的 path
        $this->assertFalse($imageIcon->validate([
            'type' => 'image',
            'path' => ''
        ]));
    }
    
    public function test_image_icon_generates_random()
    {
        $imageIcon = new ImageIcon();
        $data = $imageIcon->generateRandom();
        
        $this->assertIsArray($data);
        $this->assertEquals('image', $data['type']);
        $this->assertArrayHasKey('path', $data);
        
        // 隨機生成的應該是範例路徑
        $this->assertStringContainsString('/storage/', $data['path']);
        
        $this->assertTrue($imageIcon->validate($data));
    }
    
    public function test_get_default_for_user()
    {
        $textIcon = new TextIcon();
        $data = $textIcon->getDefaultForUser('王小明');
        
        $this->assertEquals('text', $data['type']);
        $this->assertEquals('王小', $data['text']); // 取前兩個字
        $this->assertEquals('#6366f1', $data['backgroundColor']);
        $this->assertEquals('#ffffff', $data['textColor']);
        
        // 英文名字
        $data = $textIcon->getDefaultForUser('John Doe');
        $this->assertEquals('JD', $data['text']); // 取首字母
        
        // 單個字
        $data = $textIcon->getDefaultForUser('A');
        $this->assertEquals('A', $data['text']);
    }
    
    public function test_get_default_for_organization()
    {
        $heroIcon = new HeroIcon();
        $data = $heroIcon->getDefaultForOrganization();
        
        $this->assertEquals('hero_icon', $data['type']);
        $this->assertEquals('office-building', $data['icon']);
        $this->assertEquals('outline', $data['style']);
        $this->assertEquals('#faf5ff', $data['backgroundColor']);
        $this->assertEquals('#7c3aed', $data['iconColor']);
    }
    
    public function test_get_default_for_team()
    {
        $bsIcon = new BootstrapIcon();
        $data = $bsIcon->getDefaultForTeam();
        
        $this->assertEquals('bootstrap_icon', $data['type']);
        $this->assertEquals('bi-people', $data['icon']);
        $this->assertEquals('outline', $data['style']);
        $this->assertEquals('#dbeafe', $data['backgroundColor']);
        $this->assertEquals('#2563eb', $data['iconColor']);
    }
}