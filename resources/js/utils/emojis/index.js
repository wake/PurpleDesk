/**
 * Emoji Categories Index and Manager
 * Source: Unicode 16.0 emoji-test.txt
 * Auto-generated from official Unicode data
 * Total: 3,781 emojis across 9 categories
 */

export { SMILEYS_EMOTION_EMOJIS } from './smileys-emotion.js';
export { PEOPLE_BODY_EMOJIS } from './people-body.js';
export { ANIMALS_NATURE_EMOJIS } from './animals-nature.js';
export { FOOD_DRINK_EMOJIS } from './food-drink.js';
export { TRAVEL_PLACES_EMOJIS } from './travel-places.js';
export { ACTIVITIES_EMOJIS } from './activities.js';
export { OBJECTS_EMOJIS } from './objects.js';
export { SYMBOLS_EMOJIS } from './symbols.js';
export { FLAGS_EMOJIS } from './flags.js';

// Combined emoji categories with lazy loading
export const EMOJI_CATEGORIES = {
  smileys_emotion: () => import('./smileys-emotion.js').then(m => m.SMILEYS_EMOTION_EMOJIS),
  people_body: () => import('./people-body.js').then(m => m.PEOPLE_BODY_EMOJIS),
  animals_nature: () => import('./animals-nature.js').then(m => m.ANIMALS_NATURE_EMOJIS),
  food_drink: () => import('./food-drink.js').then(m => m.FOOD_DRINK_EMOJIS),
  travel_places: () => import('./travel-places.js').then(m => m.TRAVEL_PLACES_EMOJIS),
  activities: () => import('./activities.js').then(m => m.ACTIVITIES_EMOJIS),
  objects: () => import('./objects.js').then(m => m.OBJECTS_EMOJIS),
  symbols: () => import('./symbols.js').then(m => m.SYMBOLS_EMOJIS),
  flags: () => import('./flags.js').then(m => m.FLAGS_EMOJIS),
};

// Category metadata
export const EMOJI_CATEGORY_INFO = {
  smileys_emotion: {
    id: 'smileys_emotion',
    name: 'Smileys & Emotion',
    icon: '😀',
    priority: 'immediate',
    subgroups: 16,
    count: 169
  },
  people_body: {
    id: 'people_body',
    name: 'People & Body',
    icon: '👤',
    priority: 'high',
    subgroups: 16,
    count: 2261
  },
  animals_nature: {
    id: 'animals_nature',
    name: 'Animals & Nature',
    icon: '🐾',
    priority: 'high',
    subgroups: 8,
    count: 159
  },
  food_drink: {
    id: 'food_drink',
    name: 'Food & Drink',
    icon: '🍔',
    priority: 'medium',
    subgroups: 7,
    count: 131
  },
  travel_places: {
    id: 'travel_places',
    name: 'Travel & Places',
    icon: '✈️',
    priority: 'medium',
    subgroups: 11,
    count: 218
  },
  activities: {
    id: 'activities',
    name: 'Activities',
    icon: '⚽',
    priority: 'low',
    subgroups: 5,
    count: 85
  },
  objects: {
    id: 'objects',
    name: 'Objects',
    icon: '💡',
    priority: 'low',
    subgroups: 18,
    count: 264
  },
  symbols: {
    id: 'symbols',
    name: 'Symbols',
    icon: '❤️',
    priority: 'medium',
    subgroups: 14,
    count: 224
  },
  flags: {
    id: 'flags',
    name: 'Flags',
    icon: '🏁',
    priority: 'low',
    subgroups: 3,
    count: 270
  }
};

// EmojiManager class for managing emoji loading and searching
class EmojiManager {
  constructor() {
    this.loadedCategories = new Set();
    this.categoryCache = new Map();
    this.searchIndex = null;
    this.loadingPromises = new Map();
  }

  // Get loading status
  getEmojiLoadingStatus() {
    const total = Object.keys(EMOJI_CATEGORIES).length;
    const loaded = this.loadedCategories.size;
    const loading = this.loadingPromises.size;
    
    return {
      total,
      loaded,
      loading,
      progress: Math.round((loaded / total) * 100),
      categories: Array.from(this.loadedCategories)
    };
  }

  // Preload popular emoji categories
  async preloadPopularEmojiCategories() {
    const popularCategories = ['smileys_emotion', 'people_body'];
    console.log('📱 EmojiManager: 預載入熱門分類', popularCategories);
    
    const promises = popularCategories.map(id => this.loadCategory(id));
    await Promise.all(promises);
    
    console.log('✅ EmojiManager: 熱門分類載入完成');
  }

  // Load emojis by priority
  async loadEmojisByPriority() {
    const priorityGroups = {
      immediate: [],
      high: [],
      medium: [],
      low: []
    };

    // Group categories by priority
    for (const [id, info] of Object.entries(EMOJI_CATEGORY_INFO)) {
      priorityGroups[info.priority].push(id);
    }

    // Load by priority
    console.log('🔄 EmojiManager: 開始漸進式載入');
    
    for (const priority of ['immediate', 'high', 'medium', 'low']) {
      if (priorityGroups[priority].length > 0) {
        setTimeout(() => {
          priorityGroups[priority].forEach(id => {
            if (!this.loadedCategories.has(id)) {
              this.loadCategory(id);
            }
          });
        }, priority === 'immediate' ? 0 : priority === 'high' ? 100 : priority === 'medium' ? 500 : 1000);
      }
    }
  }

  // Load a specific category
  async loadCategory(categoryId) {
    if (this.loadedCategories.has(categoryId)) {
      return this.categoryCache.get(categoryId);
    }

    if (this.loadingPromises.has(categoryId)) {
      return this.loadingPromises.get(categoryId);
    }

    const loader = EMOJI_CATEGORIES[categoryId];
    if (!loader) {
      throw new Error(`Unknown emoji category: ${categoryId}`);
    }

    const loadPromise = loader()
      .then(data => {
        this.categoryCache.set(categoryId, data);
        this.loadedCategories.add(categoryId);
        this.loadingPromises.delete(categoryId);
        
        // Build search index for this category
        this.buildSearchIndexForCategory(categoryId, data);
        
        return data;
      })
      .catch(error => {
        console.error(`Failed to load emoji category ${categoryId}:`, error);
        this.loadingPromises.delete(categoryId);
        throw error;
      });

    this.loadingPromises.set(categoryId, loadPromise);
    return loadPromise;
  }

  // Build search index for a category
  buildSearchIndexForCategory(categoryId, data) {
    if (!this.searchIndex) {
      this.searchIndex = new Map();
    }

    const categoryInfo = EMOJI_CATEGORY_INFO[categoryId];
    
    // Index all emojis in the category
    Object.entries(data).forEach(([subgroup, emojis]) => {
      emojis.forEach(item => {
        const searchKey = `${item.emoji} ${item.name} ${categoryInfo.name} ${subgroup}`.toLowerCase();
        this.searchIndex.set(item.emoji, {
          ...item,
          category: categoryInfo.name,
          categoryId,
          subgroup,
          searchKey
        });
      });
    });
  }

  // Search emojis
  async searchEmojis(query) {
    if (!query || query.trim().length === 0) {
      return [];
    }

    const searchTerm = query.toLowerCase().trim();
    const results = [];

    // Ensure at least popular categories are loaded
    await this.preloadPopularEmojiCategories();

    // Search in loaded categories
    if (this.searchIndex) {
      for (const [emoji, data] of this.searchIndex.entries()) {
        if (data.searchKey.includes(searchTerm)) {
          results.push({
            emoji: data.emoji,
            name: data.name,
            category: data.category,
            categoryId: data.categoryId,
            subgroup: data.subgroup
          });
        }
      }
    }

    // Limit results for performance
    return results.slice(0, 50);
  }

  // Get emojis by category
  async getEmojisByCategory(categoryId) {
    const data = await this.loadCategory(categoryId);
    const categoryInfo = EMOJI_CATEGORY_INFO[categoryId];
    
    const emojis = [];
    Object.entries(data).forEach(([subgroup, items]) => {
      items.forEach(item => {
        emojis.push({
          ...item,
          category: categoryInfo.name,
          categoryId,
          subgroup
        });
      });
    });
    
    return emojis;
  }

  // Get all categories info
  getCategoriesInfo() {
    return Object.values(EMOJI_CATEGORY_INFO);
  }

  // Clear cache
  clearCache() {
    this.categoryCache.clear();
    this.loadedCategories.clear();
    this.searchIndex = null;
    this.loadingPromises.clear();
  }
}

// Create singleton instance
const emojiManager = new EmojiManager();

export default emojiManager;
