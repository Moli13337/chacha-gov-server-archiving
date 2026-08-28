<template>
	<div class="share-container">
		<div 	v-if="update">
			<div class="title list-title"><p>活动展示</p></div>
			<div class="list-item-box">
				<div class="sub-title-box">
					<div class="sub-title">
						<img
							class="tip_activity"
							src="~assets/images/share/tip_activity.png"
						><p>近期活动</p>
					</div> <p
						class="more"
						@click="handleActivityListmore(1)"
					>查看更多>></p></div>
				<activity-list
					:activityList="activityList"
					@onRegistChange="handleRegistChange"
				/>
			</div>
		</div>
		<div>
			<div class="list-item-box">
				<div class="sub-title-box">
					<div class="sub-title">
						<img
							class="tip_activity"
							src="~assets/images/share/tip_activity.png"
						><p>往期活动</p>
					</div> <p
						class="more"
						@click="handleActivityListmore(1)"
					>查看更多>></p></div>
				<activity-list
					:activityList="endActivityList"
					@onRegistChange="handleRegistChange"
				/>
			</div>
		</div>
		<div>
			<p class="title">省级服务平台</p>
			<div class="link-box">
				<ul class="item-box">
					<li
						class="item"
						v-for="(item, index) in provincialLinks"
						:key="index"
					>
						<a
							target="_blank"
							:href="item.link"
						>	<img :src="item.img"></a>
						<p class="tip">[<span class="bold">
							<img
								class="icon_link"
								src="~assets/images/share/icon_link.png"
							>链接: </span><a
							class="link"
							:href="item.link"
							target="_blank"
						>{{item.title}}</a> /
							<span @click="handleLookCode(item)">
								<span
									class="bold"
								>点击扫描:</span> 微信二维码]
							</span></p>
					</li>
				</ul>
			</div>
		</div>
		<div>
			<p class="title">市级服务平台</p>
			<div class="link-box">
				<ul class="item-box">
					<li
						class="item"
						v-for="(item, index) in municipalLinks"
						:key="index"
					>
						<a
							target="_blank"
							:href="item.link"
						>	<img :src="item.img"></a>
						<p class="tip">[<span class="bold"><img
							class="icon_link"
							src="~assets/images/share/icon_link.png"
						>链接: </span><a
							class="link"
							:href="item.link"
							target="_blank"
						>{{item.title}}</a> /<span @click="handleLookCode(item)">
							<span
								class="bold"
							>点击扫描:</span> 微信二维码]
						</span></p>
					</li>
				</ul>
			</div>
		</div>
		<div>
			<p class="title">区级服务平台</p>
			<div class="link-box">
				<ul class="item-box">
					<li
						class="item"
						v-for="(item, index) in districtLinks"
						:key="index"
					>
						<a
							target="_blank"
							:href="item.link"
						>	<img :src="item.img"></a>
						<p class="tip">[<span class="bold"><img
							class="icon_link"
							src="~assets/images/share/icon_link.png"
						>链接: </span><a
							class="link"
							:href="item.link"
							target="_blank"
						>{{item.title}}</a> /<span @click="handleLookCode(item)">
							<span
								class="bold"
							>点击扫描:</span> 微信二维码]
						</span></p>
					</li>
				</ul>
				<div class="sub-box">
					<div class="subtitle"><img
						class="tip_activity"
						src="~assets/images/share/icon_account_share.png"
					><p>公众号分享</p></div>
					<qa-code-carousel
						:qrCodeList="qrCodeList"
						:perCountRow="4"
					/>
				</div>
				<div class="sub-box">
					<div class="subtitle"><img
						class="tip_activity"
						src="~assets/images/share/icon_product_share.png"
					><p>产品分享</p></div>
					<div class="link-box">
						<ul>
							<li><span class="bold">友情链接:</span>
								<span><a
									class="link"
									href="https://www.chacha.top/"
									target="_blanck"
								>察察政策通</a>
								</span>
							</li>
						</ul>
					</div>
				</div>
			</div>
		</div>
		<el-dialog
			:title="enterpriseInfo.title"
			:visible.sync="codeVisible"
			width="25%"
		>
			<div
				v-if="enterpriseInfo.codeimg"
				style="display: flex;  justify-content: center;"
			>
				<img
					:src="enterpriseInfo.codeimg"
					style="width: 100px; height: 100px"
				>
			</div>
			<p v-else>暂无二维码</p>
			<span
				slot="footer"
				class="dialog-footer"
			>
			</span>
		</el-dialog>
	</div>
</template>
<script>
import {
	ACTIVITY_LIST,
} from '@/utils/urls';
import ActivityList from '@/components/share/activity_list';
import QaCodeCarousel from '@/components/share/qa-code-carousel';
import {
	municipalLinks,
	districtLinks,
	provincialLinks,
	qrCodeList
} from './activity/utils.js';
export default {
	components: {
		ActivityList,
		QaCodeCarousel
	},
	data() {
		return {
			municipalLinks,
			districtLinks,
			provincialLinks,
			qrCodeList,
			codeVisible: false,
			enterpriseInfo: {},
			update: true
		};
	},
	asyncData({$axios, query}) {
		let registParams = {
			page: 1,
			per_page: 3
		};
		let endParams = {
			status: 3,
			page: 1,
			per_page: 3
		};

		if (query.keyword) {
			endParams.keyword = query.keyword;
			registParams.keyword = query.keyword;
		}
		return Promise.all([
			$axios.get(ACTIVITY_LIST, {params: registParams}),
			$axios.get(ACTIVITY_LIST, {params: endParams}),
		])
			.then(([activityList, endActivityList]) => ({
				activityList: activityList.data || [],
				endActivityList: endActivityList.data || []
			}))
			.catch(e => {
				console.log(e);
			});
	},
	watch: {
		$route() {
			this.featchActivityList();
		}
	},
	methods: {
		handleActivityListmore(status) {
			console.log('status', status);
			if (status == 3) {
				this.$router.push({
					path: '/share/activity',
					query: {
						status: status
					}
				});
			} else {
				this.$router.push('/share/activity');
			}
		},
		featchActivityList() {
			let registParams = {
				page: 1,
				per_page: 3
			};
			let endParams = {
				status: 3,
				page: 1,
				per_page: 3
			};

			Promise.race([
				this.$axios.get(ACTIVITY_LIST, {params: registParams}),
				this.$axios.get(ACTIVITY_LIST, {params: endParams}),
			])
				.then(([activityList, endActivityList]) => {
					this.activityList = activityList.data || [],
					this.endActivityList = endActivityList.data || [];
				})
				.catch(e => {
					console.log(e);
				});
		},
		handleLookCode(item) {
			this.enterpriseInfo = item;
			this.codeVisible = true;
		},
		handleRegistChange(id) {
			console.log('handleRegistChange', id);

			this.activityList = this.activityList.map(it => {
				if (it.id === id) {
					return {
						...it,
						apply_count: 1
					};
				}
				return it;
			});
			this.endActivityList = this.endActivityList.map(it => {
				if (it.id === id) {
					return {
						...it,
						apply_count: 1
					};
				}
				return it;
			});
		}
	},
};
</script>
<style lang="less">
@import '~assets/css/common_avairail.less';
  .share-container {
    border: 1px solid @defaultBorderColor;
    background: @backGroundColor;
    .img-box {
      width: 400px;
      margin: auto;
      text-align: center;
    }
    .tip-bold {
      font-size: 21px;
      font-weight: 500;
      padding: 10px 0;
    }
    .tip-small {
      font-size: 16px;
      color: @labelText;
    }
    .title {
      background: url('~assets/images/share/bg_title.png');
      height: 49px;
      width: 100%;
      font-weight:bold;
      color:rgba(255,255,255,1);
      font-size:18px;
      line-height: 49px;
      padding-left: 30px;
    }
    .list-item-box {
      padding:  0 20px;
    }
    .link-box {
      padding: 20px 0 20px 20px;
      .item {
        display: inline-block;
        width: 545px;
        margin-right: 20px;
        font-size: 16px;
      }
      .tip {
        margin: 10px 0;
        .link {
          &:hover {
            color: #036DB4;
          }
        }
        .icon_link {
        width: 14px;
        height: 14px;
        display: inline-block;
        margin-right: 5px;
      }
      }
      .bold {
        font-weight: 500;
        color: #3B3B3B;
      }
      .bold:hover {
        cursor: pointer;
      }
      .sub-box {
        padding-right: 20px;
          .subtitle {
            height: 35px;
            line-height: 35px;
            padding-left: 20px;
            background: url('~assets/images/title-tip.png');
            background-size: 100% 100%;
            color: #036DB4;
            font-weight:bold;
            display: flex;
            align-items: center;
            img {
              width: 18px;
              height: 12px;
              margin-right: 5px;
            }
          }

          .link {
            &:hover {
              color: #3895F1
            }
          }
      }

    }
    .list-title {
      display: flex;
      justify-content: space-between;
      padding-right: 20px;
      .more {
        color:#3B3B3B;
      }
    }
    .sub-title-box {
      width: 100%;
      height:35px;
      border:2px solid rgba(224,224,224,1);
      background:linear-gradient(180deg,rgba(251,251,251,1) 0%,rgba(235,235,235,1) 100%);
      margin-top: 20px;
      font-size:16px;
      font-weight:bold;
      line-height:21px;
      color:rgba(0,81,146,1);
      display: flex;
      justify-content: space-between;
      align-items: center;
      padding: 0 20px;
      .sub-title {
        display: flex;
        align-items: center;
        img {
          margin-right: 5px;
        }
      }
    }
    .tip_activity {
        width: 14px;
        height: 18px;
      }
    .list_item {
      height: 100px;
      border: 1px solid #eeeeee;
      margin-bottom: 20px;
      display: flex;
      justify-content: space-between;
      .iamge-box {
        width: 100px;
      }
      .text-box {
        flex: 1;
        display: flex;
        .text-content {
          flex: 1;
        }
        .btn-box {
          width: 100px;
        }
      }
    }
  }
</style>

